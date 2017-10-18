<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Acl;

/**
 * Base class for Allow and Deny policies
 *
 * Example
 * ```php
 * use Longman\TelegramBot\Acl\Allow;
 * use Longman\TelegramBot\Acl\Deny;
 * 
 * $telegram->setAcl([
 *     new Deny(['halt'], [123,124]),
 *     new Allow(['echo','ping']),
 *     new Allow([], [123]),
 *     new Deny
 * ]);
 * ```
 * This will deny 'halt' for user 123 and 124,
 * allow 'echo' and 'ping' for everyone,
 * allow all commands (except 'halt') for user 123
 * and deny everything else.
 */
abstract class Policy
{
    /**
     * Commands list
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Users list
     *
     * @var array
     */
    protected $users = [];

    /**
     * Policy constructor.
     *
     * Empty array will match all (commands or users).
     * If both commands and users are empty, any command for any user will be matched.
     *
     * @param array $commands Commands list
     * @param array $users Users list
     */
    public function __construct(array $commands = [], array $users = [])
    {
        if (!empty($commands)) $this->commands = array_map('strtolower', $commands);
        $this->users = $users;
    }

    /**
     * Check if policy matches and allows command and user
     *
     * Returns null if policy does not match.
     *
     * @param string $command
     * @param string $user
     *
     * @return null|bool
     */
    public function allow($command, $user)
    {
        if (!empty($this->commands) && !in_array(strtolower($command), $this->commands)) {
            return null;
        }
        if (!empty($this->users) && !in_array($user, $this->users)) {
            return null;
        }

        return $this instanceOf Allow;
    }

    /**
     * Check if policy matches and denies command and user
     *
     * Returns null if policy does not match.
     *
     * @param string $command
     * @param string $user
     *
     * @return null|bool
     */
    public function deny($command, $user)
    {
        $allow = $this->allow($command, $user);
        return is_null($allow) ? $allow : !$allow;
    }
}
