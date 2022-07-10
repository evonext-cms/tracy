<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy\Middleware;

use EvoNext\Tracy\DebuggerManager;
use EvoNext\Tracy\Events\BeforeBarRender;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RenderBar
{
    private DebuggerManager $debuggerManager;
    private Dispatcher      $events;

    public function __construct(DebuggerManager $debuggerManager, Dispatcher $events)
    {
        $this->debuggerManager = $debuggerManager;
        $this->events          = $events;
    }

    /**
     * handle.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        return $request->has('_tracy_bar') === true
            ? $this->keepFlashSession($request, $next)
            : $this->render($request, $next);
    }

    /**
     * keepFlashSession.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return Response
     */
    private function keepFlashSession($request, $next)
    {
        $type = $request->get('_tracy_bar');
        if ($request->hasSession() === true && in_array($type, ['js', 'css'], true) === false) {
            $request->session()->reflash();
        }

        return $next($request);
    }

    /**
     * render.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return Response
     */
    private function render($request, $next)
    {
        $this->debuggerManager->dispatch();

        $response = $next($request);

        $ajax = $request->ajax();

        if ($this->reject($response, $ajax) === true) {
            return $response;
        }

        $method = method_exists($this->events, 'dispatch') ? 'dispatch' : 'fire';
        $this->events->{$method}(new BeforeBarRender($request, $response));

        $response->setContent(
            $this->debuggerManager->shutdownHandler($response->getContent(), $ajax)
        );

        return $response;
    }

    /**
     * reject.
     *
     * @param Response $response
     * @param bool $ajax
     * @return bool
     */
    private function reject(Response $response, $ajax)
    {
        if (
            $response instanceof BinaryFileResponse ||
            $response instanceof StreamedResponse ||
            $response instanceof RedirectResponse
        ) {
            return true;
        }

        if ($ajax === true) {
            return false;
        }

        $contentType = strtolower($response->headers->get('Content-Type'));
        $accepts     = $this->debuggerManager->accepts();
        if ((empty($contentType) === true && $response->getStatusCode() >= 400) ||
            count($accepts) === 0
        ) {
            return false;
        }

        foreach ($accepts as $accept) {
            if (strpos($contentType, $accept) !== false) {
                return false;
            }
        }

        return true;
    }
}
