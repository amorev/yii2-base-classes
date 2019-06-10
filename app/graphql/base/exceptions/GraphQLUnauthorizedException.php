<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 15.04.2019
 * Time: 23:01
 */

namespace Zvinger\BaseClasses\app\graphql\base\exceptions;


use GraphQL\Error\ClientAware;
use Throwable;

class GraphQLUnauthorizedException extends \Exception implements ClientAware
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $message = 'Unauthorized';
        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns true when exception message is safe to be displayed to a client.
     *
     * @return bool
     *
     * @api
     */
    public function isClientSafe()
    {
        return true;
    }

    /**
     * Returns string describing a category of the error.
     *
     * Value "graphql" is reserved for errors produced by query parsing or validation, do not use it.
     *
     * @return string
     *
     * @api
     */
    public function getCategory()
    {
        return 'unauthorized';
    }
}
