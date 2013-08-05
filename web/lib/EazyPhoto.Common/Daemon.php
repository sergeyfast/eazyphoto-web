<?php
    /**
     * Daemon
     * 
     */
    class Daemon {
        public $name = null;
        
        public $package = null;
        
        public $method = array();
        
        public $startDate = null;
        
        public $endDate = null;
        
        public $maxExecutionTime = null;
        
        public $params = array();
        
        public $active = null;
        
        /**
         * Get Instance
         *
         * @param Daemon $parameters
         */
        public static function GetInstance( $parameters ) {
            $daemon = new Daemon();
            
            $daemon->package          = $parameters["package"];
            $daemon->method           = $parameters["method"];
            $daemon->name             = $parameters["title"];
            $daemon->maxExecutionTime = $parameters["maxExecutionTime"];
            $daemon->startDate        = $parameters["startDate"];
            $daemon->endDate          = $parameters["endDate"];
            $daemon->active           = $parameters["active"];
            $daemon->params           = $parameters["params"];
            
            if( $daemon->active === null ) {
                $daemon->active = true;
            }
            
            if ( $daemon->maxExecutionTime == null ) {
                $daemon->maxExecutionTime = '00:03:00';
            }
            
            return $daemon;
        }
        
        
        /**
         * Can Run?
         *
         * @return bool
         */
        public function CanRun() {
            if ( !$this->active )  {
                return false;
            }
            
            $now = DateTimeWrapper::Now();
            
            if ( !empty( $this->startDate ) ) {
                if (  $now <= Convert::ToDateTime( $this->startDate ) ) {
                    return false;
                }
            }
            
            if ( !empty( $this->endDate ) ) {
                if ( $now >= Convert::ToDateTime( $this->endDate ) ) {
                    return false;
                }
            }

            return true;           
        }
        
        
        /**
         * Get Method Name
         *
         * @return unknown
         */
        public function GetMethodName() {
            if ( is_array( $this->method ) ) {
                list($m, $n) = $this->method;
                
                return $m . "::" . $n;
            } else {
                return $this->method;
            }
        }
        
        
        /**
         * Get Daemon Lock
         *
         * @return DaemonLock
         */
        public function GetDaemonLock()  {
            $daemonLock = new DaemonLock();
            $daemonLock->maxExecutionTime = $this->maxExecutionTime;
            $daemonLock->methodName       = $this->GetMethodName();
            $daemonLock->packageName      = $this->package;
            $daemonLock->title            = $this->name;
            $daemonLock->isActive         = null;
            
            return $daemonLock;
        }
        
        
        /**
         * Lock
         * @return bool
         */
        public function Lock() {
            $lock = $this->CheckLock();
            if ( !empty( $lock ) ) {
                // check lock for active
                if ( $lock->isActive ) {
                    Logger::Info(  "Lock {$lock->title} is active");
                    return false;
                } else {
                    Logger::Warning(  "Flusing inactive lock {$lock->title}" );
                    DaemonLockFactory::Delete( $lock );
                }
            }

            $result = DaemonLockFactory::Add( $this->GetDaemonLock() );
            Logger::Info(  "Locked {$this->name}");
            
            return true;
        }
        
        
        /**
         * Unlock
         *
         */
        public function Unlock() {
            $lock = $this->CheckLock();
            if ( !empty( $lock ) ) {
                DaemonLockFactory::Delete( $lock );
            }
            
            return true;
        }
        
        
        /**
         * Check Lock
         *
         * @return DaemonLock
         */
        public function CheckLock() {
            $dl = $this->GetDaemonLock();
            
            $lock = DaemonLockFactory::GetOne( 
                array(
                    "packageName"   => $dl->packageName
                    , "methodName"  => $dl->methodName
                    , "title"       => $dl->title
                )
            );
            
            return $lock;
        }
        
        
        
        /**
         * Run Daemon
         *
         */
        public function Run() {
            if ( !$this->CanRun() ) {
                Logger::Info(  "{$this->name} couldn't be run.");
                return false;
            }
            
            set_time_limit( 0 );
            
            if ( !$this->Lock() ) {
                Logger::Warning(  "Failed to lock {$this->name}");
                return false;
            }
            
            try {
                Package::Load( $this->package );
                call_user_func_array( $this->method, array( $this->params ) );
            } catch ( Exception $e ) {
                Logger::Error(  "{$this->name}: exeption in {$e->getMessage()}" );
            }
            
            $this->Unlock();
            
            return true;
        }
    }
?>