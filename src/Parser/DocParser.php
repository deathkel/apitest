<?php

namespace Deathkel\Apitest\Parser;

class DocParser
{
    private static $instance;
    private $params = array();

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new DocParser();
        }
        return self::$instance;
    }

    function parse($doc = '')
    {
        if ($doc == '') {
            return $this->params;
        }
        // Get the comment  去除/**   */
        if (preg_match('#^/\*\*(.*)\*/#s', $doc, $comment) === false)
            return $this->params;
        $comment = trim($comment [1]);
        // Get all the lines and strip the * from the first character,去除*
        if (preg_match_all('#^\s*\*(.*)#m', $comment, $lines) === false)
            return $this->params;
        $this->parseLines($lines [1]);

        return $this->params;
    }

    public function resetParams()
    {
        $this->params = null;
    }

    private function parseLines($lines)
    {
        foreach ($lines as $k => $line) {
            $parsedLine = $this->parseLine($line); // Parse the line
            if (is_array($parsedLine)) {//成功设置
                $lastParsedLine = $parsedLine;//此行前最近有@的行
            } elseif ($parsedLine !== false && isset($lastParsedLine)) {
                if (!empty($lastParsedLine['param']) && $lastParsedLine['param'] !== 'param') {//@param不允许换行
                    $this->setParam($lastParsedLine['param'], $parsedLine);//归于此行前最近有@的行
                }
            }
        }

    }

    private function parseLine($line)
    {
        // trim the whitespace from the line

        $line = trim($line);

        if (empty ($line))
            return false; // Empty line

        if (strpos($line, '@') === 0) {
            if (strpos($line, ' ') > 0) {
                // Get the parameter name
                $param = substr($line, 1, strpos($line, ' ') - 1);
                $value = substr($line, strlen($param) + 2); // Get the value
            } else {
                $param = substr($line, 1);
                $value = '';
            }

            //成功设置后返回true
            if ($this->setParam($param, $value))
                return ['param' => $param, 'value' => $value];
        }

        return $line;
    }

    private function getLineParamName($line)
    {
        $line = trim($line);
        if (strpos($line, '@') === 0) {
            if (strpos($line, ' ') > 0) {
                // Get the parameter name
                $param = substr($line, 1, strpos($line, ' ') - 1);
            } else {
                $param = substr($line, 1);

            }
            return $param;
        }
    }

    private function setParam($param, $value)
    {
        if ($param == 'param'){
            $value = $this->formatParamOrReturn($value);
        }

        if ($param == 'class')
            list ($param, $value) = $this->formatClass($value);

        if (empty ($this->params [$param])) {
            $this->params [$param] = array($value);//use Array
        } else if ($param == 'param') {
            $arr = $this->params[$param];
            array_push($arr, $value);
            $this->params[$param] = $arr;
        } else {
            $arr = $this->params[$param];
            array_push($arr, $value);
            $this->params[$param] = $arr;
        }
        return true;
    }

    private function formatClass($value)
    {
        $r = preg_split("[\(|\)]", $value);
        if (is_array($r)) {
            $param = $r [0];
            parse_str($r [1], $value);
            foreach ($value as $key => $val) {
                $val = explode(',', $val);
                if (count($val) > 1)
                    $value [$key] = $val;
            }
        } else {
            $param = 'Unknown';
        }
        return array(
            $param,
            $value
        );
    }

    private function formatParamOrReturn($string)
    {
        $array = explode(' ', $string);
        $res['type'] = isset($array[0]) ? $array[0] : '';
        $res['name'] = isset($array[1]) ? $array[1] : '';
        $res['default'] = isset($array[2]) ? $array[2] : '';
        return $res;
    }
}