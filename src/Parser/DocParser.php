<?php
namespace ApiTest\Parser;

class DocParser {
    private static $instance;
    private $params = array ();

    private function DocParser(){
    }

    public static function getInstance(){
        if(self::$instance==null) {
            self::$instance=new DocParser();
        }
        return self::$instance;
    }

    function parse($doc = '') {
        if ($doc == '') {
            return $this->params;
        }
        // Get the comment  去除/**   */
        if (preg_match ( '#^/\*\*(.*)\*/#s', $doc, $comment ) === false)
            return $this->params;
        $comment = trim ( $comment [1] );
        // Get all the lines and strip the * from the first character,去除*
        if (preg_match_all ( '#^\s*\*(.*)#m', $comment, $lines ) === false)
            return $this->params;
        $this->parseLines ($lines [1]);

        return $this->params;
    }

    public function resetParams()
    {
        $this->params=null;
    }

    private function parseLines($lines) {
        foreach ( $lines as $line ) {
            $parsedLine = $this->parseLine ( $line ); // Parse the line
            if ($parsedLine === false && ! isset ( $this->params ['description'] )) {
                if (isset ( $desc )) {
                    // Store the first line in the short description
//                    $this->params ['description'] = implode ( PHP_EOL, $desc );
                }
                $desc = array ();
            } elseif ($parsedLine !== false) {
                $desc [] = $parsedLine; // Store the line in the long description
            }
        }
        $desc = implode ( ' ', $desc );
        if (! empty ( $desc ))
            $this->params ['long_description'] = $desc;
    }

    private function parseLine($line) {
        // trim the whitespace from the line
        $line = trim ( $line );

        if (empty ( $line ))
            return false; // Empty line

        if (strpos ( $line, '@' ) === 0) {
            if (strpos ( $line, ' ' ) > 0) {
                // Get the parameter name
                $param = substr ( $line, 1, strpos ( $line, ' ' ) - 1 );
                $value = substr ( $line, strlen ( $param ) + 2 ); // Get the value
            } else {
                $param = substr ( $line, 1 );
                $value = '';
            }
            // Parse the line and return false if the parameter is valid
            if ($this->setParam ( $param, $value ))
                return false;
        }

        return $line;
    }

    private function setParam($param, $value) {
        if ($param == 'param' || $param == 'return')
            $value = $this->formatParamOrReturn ( $value );
        if ($param == 'class')
            list ( $param, $value ) = $this->formatClass ( $value );

        if (empty ( $this->params [$param] )) {
            $this->params [$param] = array($value);//use Array
        } else if ($param == 'param') {
            $arr=$this->params[$param];
            array_push($arr,$value);
            $this->params[$param]=$arr;
        } else {
            $arr=$this->params[$param];
            array_push($arr,$value);
            $this->params[$param]=$arr;
        }
        return true;
    }

    private function formatClass($value) {
        $r = preg_split ( "[\(|\)]", $value );
        if (is_array ( $r )) {
            $param = $r [0];
            parse_str ( $r [1], $value );
            foreach ( $value as $key => $val ) {
                $val = explode ( ',', $val );
                if (count ( $val ) > 1)
                    $value [$key] = $val;
            }
        } else {
            $param = 'Unknown';
        }
        return array (
            $param,
            $value
        );
    }

    private function formatParamOrReturn($string) {
        $pos = strpos ( $string, ' ' );

        $type = substr ( $string, 0, $pos );//参数类型
        return substr ( $string, $pos);
    }
}