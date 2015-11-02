<?php

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class Json
{
    /**
     * @param mixed $value
     * @param int $options
     * @param int $depth
     *
     * @return string
     *
     * @throws \InvalidArgumentException when
     *  JSON_ERROR_STATE_MISMATCH
     *  JSON_ERROR_CTRL_CHAR
     *  JSON_ERROR_SYNTAX
     *  JSON_ERROR_UTF8
     *  JSON_ERROR_INF_OR_NAN
     *  JSON_ERROR_UNSUPPORTED_TYPE
     * @throws \OverflowException when
     *  JSON_ERROR_DEPTH
     *  JSON_ERROR_RECURSION
     * @throws \UnexpectedValueException otherwise
     *
     * @api
     */
    public function encode($value, $options = 0, $depth = 512)
    {
        $json = json_encode($value, $options, $depth);
        $error = json_last_error();
        if ($error) {
            throw $this->getException();
        }
        return $json;
    }

    /**
     * @param string $json
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException when
     *  JSON_ERROR_STATE_MISMATCH
     *  JSON_ERROR_CTRL_CHAR
     *  JSON_ERROR_SYNTAX
     *  JSON_ERROR_UTF8
     *  JSON_ERROR_INF_OR_NAN
     *  JSON_ERROR_UNSUPPORTED_TYPE
     * @throws \OverflowException when
     *  JSON_ERROR_DEPTH
     *  JSON_ERROR_RECURSION
     * @throws \UnexpectedValueException otherwise
     *
     * @api
     */
    public function decode($json, $assoc = false, $depth = 512, $options = 0)
    {
        $result = json_decode($json, $assoc, $depth, $options);
        $error = json_last_error();
        if ($error) {
            throw $this->getException();
        }
        return $result;
    }

    /**
     * @return \InvalidArgumentException|\OverflowException|\UnexpectedValueException
     */
    private function getException()
    {
        $message = json_last_error_msg();
        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
            case JSON_ERROR_RECURSION:
                return new \OverflowException($message);
            case JSON_ERROR_STATE_MISMATCH:
            case JSON_ERROR_CTRL_CHAR:
            case JSON_ERROR_SYNTAX:
            case JSON_ERROR_UTF8:
            case JSON_ERROR_INF_OR_NAN:
            case JSON_ERROR_UNSUPPORTED_TYPE:
                return new \InvalidArgumentException($message);
            default:
                return new \UnexpectedValueException($message);
        }
    }
}