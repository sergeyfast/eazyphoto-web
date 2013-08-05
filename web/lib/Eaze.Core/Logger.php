<?php
    define( 'ELOG_NONE',    0 );
    define( 'ELOG_FATAL',   1 );
    define( 'ELOG_ERROR',   2 );
    define( 'ELOG_WARNING', 3 );
    define( 'ELOG_INFO',    4 );
    define( 'ELOG_DEBUG',   5 );

    /**
     * Logger
     *
     * @package Eaze
     * @subpackage Core
     * @author sergeyfast
     */
    class Logger {

        /**
         * Html Output mode
         */
        const HtmlMode = 'html';


        /**
         * Fire PHP Output mode
         */
        const FirePHPMode = 'fp';

        /**
         * Text Output Mode
         */
        const TextMode = 'text';

        /**
         * Output Mode
         * Can be html or fb.
         *
         * @var string
         */
        private static $outputMode = self::HtmlMode;

        /**
         * Checkpoints
         *
         * @var array
         */
        private static $checkpoints = array();

        /**
         * Current Checkpoint Level
         *
         * @var integer
         */
        private static $currentLevel = 0;

        /**
         * Current Log Level
         * Default is ELOG_NONE
         *
         * @var int
         */
        private static $logLevel = 0;

        /**
         * Start Time
         *
         * @var float
         */
        private static $startTime = 0;


        /**
         * Is First output (used for inline styles)
         * @var bool
         */
        private static $isFirstOutput = false;

        /**
         * Flag is used by {@see getCallingMethod} to decrease calling method.
         * After {@see getCallingMethod} is done, flag will reset automatically.
         * @var bool
         */
        private static $decreaseCallingMethod = false;


        /**
         * Initialize Logger
         * @param int $logLevel     ELOG_* constant (0..5)
         * @param string $outputTo  self::*Mode constant (text|html|fb)
         */
        public static function Init( $logLevel = ELOG_NONE, $outputTo = self::HtmlMode ) {
            Logger::$startTime = Logger::GetCurrentTime();

            Logger::LogLevel( $logLevel );
            Logger::OutputTo( $outputTo );
        }

        /**
         * Set Log Level
         *
         * @param integer $logLevel
         */
        public static function LogLevel( $logLevel ) {
            Logger::$logLevel = $logLevel;
        }


        /**
         * Get Current Log Level
         * @static
         * @return int
         */
        public static function GetCurrentLevel() {
            return Logger::$logLevel;
        }


        /**
         * Set Output mode
         *
         * @param string $mode html|fb|text
         */
        public static function OutputTo( $mode ) {
            self::$outputMode = $mode;
        }


        /**
         * Get Output Mode
         * @static
         * @return string
         */
        public static function GetOutputMode() {
            return self::$outputMode;
        }


        /**
         * Set Checkpoint
         */
        public static function Checkpoint() {
            Logger::$checkpoints[Logger::$currentLevel++] = array(
                'time'     => Logger::GetCurrentTime()
                , 'memory' => Logger::GetCurrentMemoryUsage()
            );
        }


        public static function Debug( $message, $_ = null )  {
            if ( Logger::$logLevel >= ELOG_DEBUG ) {
                $params = func_get_args();
                Logger::log( ELOG_DEBUG, $message, $params );
            }
        }

        public static function Info( $message, $_ = null ) {
            if ( Logger::$logLevel >= ELOG_INFO ) {
                $params = func_get_args();
                Logger::log( ELOG_INFO, $message, $params );
            }
        }


        public static function Warning( $message, $_ = null ) {
            if ( Logger::$logLevel >= ELOG_WARNING ) {
                $params = func_get_args();
                Logger::log( ELOG_WARNING, $message, $params );
            }
        }


        public static function Error( $message, $_ = null ) {
            if ( Logger::$logLevel >= ELOG_ERROR ) {
                $params = func_get_args();
                Logger::log( ELOG_ERROR, $message, $params );
            }
        }


        public static function Fatal( $message, $_ = null ) {
            if ( Logger::$logLevel >= ELOG_FATAL ) {
                $params = func_get_args();
                Logger::log( ELOG_FATAL, $message, $params );
            }
        }


        /**
         * Format Position
         *
         * @static
         * @param  array $trace backtrace element
         * @return string
         */
        private static function getCallingMethodString( $trace ) {
            return ( !empty( $trace['class'] ) ? $trace['class'] : '' )
                . ( !empty( $trace['type'] ) ? htmlentities( $trace['type'] ) : '' )
                . ( !empty( $trace['function'] ) ? $trace['function'] : '' );
        }


        /**
         * Flush Html Inline Style for Debug
         * @static
         * @return void
         */
        private static function flushHtmlInlineStyle() {
            $xhtml = <<<css
                <style type="text/css">
                    .eaze-logger {
                        border: 1px dotted #ccc;
                        background: #fff;
                        color: #333;
                        font-size: 11px;
                        font-family: Arial, Helvetica, sans-serif;
                        list-style-type: none;
                        left: 0;
                        margin: -1px 0 0;
                        overflow: auto;
                        padding: 3px 0;
                        top: 0;
                        width: 100%;
                        z-index: 1000;
                    }
                        .eaze-logger-info {
                            background: #fff;
                        }
                        .eaze-logger-error, .eaze-logger-fatal, .eaze-logger-warning {
                            background: #ffd9d9;
                        }
                        .eaze-logger-debug {
                            background: #dff7e3;
                        }
                        .eaze-logger div.time-before,
                        .eaze-logger div.time-after {
                            color: #666;
                            float: left;
                            overflow: hidden;
                            margin-right: 1em;
                            -o-text-overflow: ellipsis;
                            text-overflow: ellipsis;
                            width: 4.5em;
                        }
                        .eaze-logger div.time-after {
                            float: right;
                        }
                        .eaze-logger div.memory-before,
                        .eaze-logger div.memory-after {
                            color: #666;
                            float: left;
                            overflow: hidden;
                            margin-right: 1em;
                            text-overflow: ellipsis;
                            width: 4.7em;
                        }
                        .eaze-logger div.memory-after {
                            float: right;
                        }
                        .eaze-logger div.type {
                            color: #000;
                            font-weight: bold;
                            float: left;
                            padding-left: 1em;
                            width: 6em;
                        }
                        .eaze-logger div.text {
                            color: #000;
                            margin: 0 13em 0 18.5em;
                        }
                            .eaze-logger div.text pre {
                                font-size: 11px;
                                font-family: monospace;
                                color: #444;
                                margin: 0.5em 0;
                            }
                </style>
css;

            echo $xhtml;
        }


        /**
         * Save Log
         * @static
         * @param  array $result
         * @return void
         */
        private static function saveLog( $result ) {
            switch (self::$outputMode ) {
                case self::HtmlMode:
                    $checkPointTemplate = <<<xhtml
            <div class="time-after">%2.4f</div>
            <div class="memory-after">%2.4f Mb</div>
xhtml;
                    $messageTemplate = <<<xhtml
            <div class="eaze-logger eaze-logger-%s">
                <div class="time-before">%2.4f</div>
                <div class="memory-before">%2.4f Mb</div>
                <div class="type">%s</div>
                %s
                <div class="text">%s<strong>%s</strong> %s</div>
            </div>
xhtml;
                    if ( !self::$isFirstOutput ) {
                        self::$isFirstOutput = true;
                        self::flushHtmlInlineStyle();
                    }

                    // message
                    printf( $messageTemplate
                        , strtolower( $result['levelName'] )
                        , $result['relativeTime']
                        , $result['memoryUsage']
                        , $result['levelName']
                        , !empty( $result['checkPoint'] ) ? sprintf( $checkPointTemplate, $result['checkPoint'], $result['memPoint'] ) : ''
                        , $result['indentLevel']
                        , self::getCallingMethodString( $result['trace'] )
                        , $result['message']
                    );

                    break;
                case self::FirePHPMode:
                     $message =  sprintf( '[%2.4f]%s [%s Mb] %s >> %s '
                        , $result['relativeTime']
                        , $result['indentLevel']
                        , number_format( $result['memoryUsage'], 3 )
                        , $result['levelName']
                        , $result['message']
                    );

                    if ( ! empty( $result['checkPoint'] ) ) {
                        $message .= sprintf( ' [%f] [%s Mb]', $result['checkPoint'], number_format( $result['memPoint'], 3 )  );
                    }

                    fb( $message, self::convertLogLevelToFB($result['logLevel']));

                    break;
                case self::TextMode:
                    printf( '[%2.4f] [%s Mb] %s >> %s: %s %s' . PHP_EOL
                        , $result['relativeTime']
                        , $result['memoryUsage']
                        , $result['levelName']
                        , html_entity_decode( self::getCallingMethodString( $result['trace'] ) )
                        , $result['message']
                        , !empty( $result['checkPoint'] ) ? sprintf( '[%f] [%s Mb]', $result['checkPoint'], $result['memPoint'] ) : ''
                    );

                    break;
            }
        }


        /**
         * Convert LogLevel to FirePHP LogLevel
         * @static
         * @param  int $logLevel
         * @return string
         */
        private static function convertLogLevelToFB( $logLevel ){
            switch ($logLevel){
                case ELOG_DEBUG:
                case ELOG_NONE:
                    return FirePHP::LOG;
                case ELOG_INFO:
                    return FirePHP::INFO;
                case ELOG_WARNING:
                    return FirePHP::WARN;
                case ELOG_ERROR:
                case ELOG_FATAL:
                    return FirePHP::ERROR;
            }
        }


        /**
         * Log Action
         * @static
         * @param  int     $logLevel
         * @param  string  $message   debug message
         * @param  array   $args      arguments to sprintf (0 is index of message)
         * @return void
         */
        private static function log( $logLevel, $message, $args = array() ) {
            if ( Logger::$logLevel >= $logLevel ) {
                $trace = self::getCallingMethod();

                if ( !empty( $args ) && count( $args ) > 1 ) {
                    $message = vsprintf( $message, array_slice( $args, 1  ) );
                }

                $result = array(
                    'relativeTime'  => Logger::GetRelativeTime()
                    , 'memoryUsage' => Logger::GetCurrentMemoryUsage()
                    , 'indentLevel' => str_repeat( '&nbsp;', ( (Logger::$currentLevel - 1 < 0) ? 0 : Logger::$currentLevel - 1  ) * 10 )
                    , 'levelName'   => Logger::GetLevelName( $logLevel )
                    , 'message'     => $message
                    , 'logLevel'    => $logLevel
                    , 'datetime'    => date( 'c' )
                    , 'checkPoint'  => (Logger::$currentLevel > 0 ) ? Logger::getCheckpointTime( Logger::$currentLevel - 1 ) : null
                    , 'memPoint'    => (Logger::$currentLevel > 0 ) ? Logger::getCheckpointMemory( Logger::$currentLevel - 1 ) : null
                    , 'trace'       => $trace
                );

                Logger::saveLog( $result );

                if ( self::$logLevel >= $logLevel && $logLevel <= ELOG_ERROR ) {
                    Logger::Backtrace();
                }
            }

            // Flush Check point
            if ( Logger::$currentLevel > 0 ) {
                Logger::$currentLevel --;
                array_pop( Logger::$checkpoints );
            }
        }


        /**
         * Get Current Time
         *
         * @return float
         */
        public static function GetCurrentTime() {
            return microtime( true );
        }

        /**
         * Get Checkpoint Time
         *
         * @access public
         * @return float
         */
        public static function GetRelativeTime() {
            return round( (float)( Logger::GetCurrentTime() - (float) Logger::$startTime ), 6 );
        }


        /**
         * Get Relative Time To
         * @param $time
         *
         * @return float
         */
        public static function GetRelativeTimeTo( $time ) {
            return round( (float)( Logger::GetCurrentTime() - (float) $time ), 6 );
        }


        /**
         * Get Checkpoint Time.
         *
         * @return float
         */
        private static function getCheckpointTime( $level ) {
            return Logger::GetCurrentTime() - Logger::$checkpoints[$level]['time'];
        }

        /**
         * Gets Checkpoint Memory Usage.
         *
         * @param int $level  Checkpoint level
         * @return int
         */
        private static function getCheckpointMemory( $level ) {
            return ( Logger::GetCurrentMemoryUsage() - Logger::$checkpoints[$level]['memory'] );
        }

        /**
         * Get Current Memory Usage
         * @static
         * @return float MBytes
         */
        public static function GetCurrentMemoryUsage() {
            return round( ( (float)memory_get_usage()/1024 / 1024 ), 3 );
        }


        /**
         * Get Level Name
         *
         * @param integer $logLevel
         * @return string
         */
        public static function GetLevelName( $logLevel ) {
            switch ( $logLevel ) {
                case ELOG_DEBUG:
                    return 'DEBUG';
                case ELOG_INFO:
                    return 'INFO';
                case ELOG_WARNING:
                    return 'WARNING';
                case ELOG_ERROR:
                     return 'ERROR';
                case ELOG_FATAL:
                     return 'FATAL';
                default:
                    return 'NONE';
            }
        }


        /**
         * Print Backtrace
         */
        public static function Backtrace() {
            ob_start();
            debug_print_backtrace();
            $trace = ob_get_contents();
            ob_end_clean();

            $result = $trace;
            switch ( self::$outputMode ) {
                case self::HtmlMode:
                    $result = <<<xhtml
                    <div class="eaze-logger eaze-logger-info">
                        <div class="type">Backtrace</div>
                        <div class="text"><pre>{$trace}</pre></div>
                    </div>
xhtml;
                    if ( !self::$isFirstOutput ) {
                        self::$isFirstOutput = true;
                        self::flushHtmlInlineStyle();
                    }
                    break;
            }

            echo $result;
        }


        /**
         * Get Calling Method
         * @static
         * @param int $level
         * @return array
         */
        private static function getCallingMethod( $level = 3 ) {
            if ( self::$decreaseCallingMethod ) {
                $level ++;
                self::$decreaseCallingMethod = false;
            }

            $trace  = debug_backtrace();
            if ( !empty( $trace[$level] ) ) {
                $result             = $trace[$level];
                $result['fromLine'] = !empty( $trace[$level - 1] ) ? $trace[$level - 1]['line'] : $result['line'];
            } else {
                $result             = $trace[$level - 1];
                $result['fromLine'] = !empty( $trace[$level - 2] ) ? $trace[$level - 2]['line'] : $result['line'];
            }

            return $result;
        }


        /**
         * Print R
         * @static
         * @param mixed $value
         */
        public static function PrintR( $value ) {
            $trace =  self::getCallingMethod( 2 );

            ob_start();
            print_r( $value );
            $result = ob_get_contents();
            ob_end_clean();

            $template = <<<xhtml
            <div class="eaze-logger eaze-logger-info">
                <div class="type">PrintR</div>
                <div class="text"><strong>%s</strong> at line %d <pre>%s</pre></div>
            </div>
xhtml;

            if ( !self::$isFirstOutput ) {
                self::$isFirstOutput = true;
                self::flushHtmlInlineStyle();
            }

            printf( $template, self::getCallingMethodString( $trace ), $trace['fromLine'], $result );
        }


        /**
         * Var_dump
         *
         * @param mixed  $value
         * @param array $_z
         */
        public static function VarDump( $value, $_ = array() ) {
            $trace =  self::getCallingMethod( 2 );

            ob_start();
            switch( func_num_args() ) {
                case 1:
                    var_dump( $value );
                    break;
                case 0:

                    break;
                default:
                    var_dump( func_get_args() );
                    break;
            }

            $result = ob_get_contents();
            ob_end_clean();

            $template = <<<xhtml
            <div class="eaze-logger eaze-logger-info">
                <div class="type">VarDump</div>
                <div class="text"><strong>%s</strong> at line %d <pre>%s</pre></div>
            </div>
xhtml;

            if ( !self::$isFirstOutput ) {
                self::$isFirstOutput = true;
                self::flushHtmlInlineStyle();
            }

            printf( $template, self::getCallingMethodString( $trace ), $trace['fromLine'], $result );
        }



        /**
         * Mix var_dump and print_r
         * @static
         * @param mixed  $value
         */
        public static function VarPrint( $value ) {
            self::$decreaseCallingMethod = true;
            if ( !empty( $value ) && ( is_array( $value ) || is_object( $value ) ) ) {
                return self::PrintR( $value );
            } else {
                return self::VarDump( $value );
            }
        }
    }
?>