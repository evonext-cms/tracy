<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy\Exceptions;

use EvoNext\Tracy\DebuggerManager;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

class HandlerForLaravel6 implements ExceptionHandler
{
    protected ExceptionHandler $exceptionHandler;
    protected DebuggerManager  $debuggerManager;

    public function __construct(ExceptionHandler $exceptionHandler, DebuggerManager $debuggerManager)
    {
        $this->exceptionHandler = $exceptionHandler;
        $this->debuggerManager  = $debuggerManager;
    }

    /**
     * Report or log an exception.
     *
     * @param Exception $e
     * @return void
     * @throws \Throwable
     */
    public function report(Throwable $e)
    {
        $this->exceptionHandler->report($e);
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param Exception $e
     * @return bool
     */
    public function shouldReport(Throwable $e)
    {
        return $this->exceptionHandler->shouldReport($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        $response = $this->exceptionHandler->render($request, $e);

        if ($this->shouldRenderException($response) === true) {
            $_SERVER = $request->server();
            $response->setContent($this->debuggerManager->exceptionHandler($e));
        }

        return $response;
    }

    /**
     * Render an exception to the console.
     *
     * @param OutputInterface $output
     * @param Exception $e
     * @return void
     */
    public function renderForConsole($output, Throwable $e)
    {
        $this->exceptionHandler->renderForConsole($output, $e);
    }

    /**
     * shouldRenderException.
     *
     * @param Response|\Symfony\Component\HttpFoundation\Response $response
     * @return bool
     */
    protected function shouldRenderException($response)
    {
        if (
            $response instanceof RedirectResponse ||
            $response instanceof JsonResponse ||
            $response->getContent() instanceof View ||
            ($response instanceof Response && $response->getOriginalContent() instanceof View)
        ) {
            return false;
        }

        return true;
    }
}
