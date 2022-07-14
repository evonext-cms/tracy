<?php /** @var array $rows */ ?>
<h1><?php echo empty($rows) === false ? 'Logged in' : 'Unauthenticated' ?></h1>

<div class="tracy-inner Laravel-AuthPanel">
    <div class="tracy-inner-container">
        <?php if (empty($rows) === true): ?>
            <p>No identity</p>
        <?php else: ?>
            <table>
                <tbody>
                    <?php foreach ($rows as $key => $value): ?>
                        <tr>
                            <th><?php echo $key ?></th>
                            <td>
                                <?php echo Tracy\Dumper::toHtml($value, [Tracy\Dumper::LIVE => true]) ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>

                <tbody>
                    <tr>
                        <th colspan="2" style="text-align:center;">Roles</th>
                    </tr>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $k => $v): ?>
                            <tr>
                                <th><?php echo $k ?></th>
                                <td><?php echo $v ?></td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <th>*</th>
                            <td>NONE</td>
                        </tr>
                    <?php endif ?>
                </tbody>

                <tbody>
                    <tr>
                        <th colspan="2" style="text-align:center;">Permissions</th>
                    </tr>
                    <?php if ($isAdmin ?? false): ?>
                        <tr>
                            <th>*</th>
                            <td>ALL GRANTED</td>
                        </tr>
                    <?php else: ?>
                        <?php if (!empty($perms)): ?>
                            <?php foreach ($perms as $k => $v): ?>
                                <tr>
                                    <th><?php echo $k ?></th>
                                    <td><?php echo $v ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <th>*</th>
                                <td>NONE</td>
                            </tr>
                        <?php endif ?>
                    <?php endif ?>
                </tbody>

            </table>
        <?php endif ?>
    </div>
</div>
