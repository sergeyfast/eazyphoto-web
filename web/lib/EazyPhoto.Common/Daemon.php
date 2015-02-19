<?php

    use Eaze\Core\Convert;
    use Eaze\Core\DateTimeWrapper;
    use Eaze\Core\Logger;

    /**
     * Daemon
     *
     */
    class Daemon {

        public $Name;

        public $Package;

        public $Method = [ ];

        public $StartDate;

        public $EndDate;

        public $MaxExecutionTime;

        public $Params = [ ];

        public $Active;


        /**
         * Get Instance
         *
         * @param array $parameters
         * @return \Daemon
         */
        public static function GetInstance( $parameters ) {
            $daemon = new Daemon();

            $daemon->Package          = $parameters['package'];
            $daemon->Method           = $parameters['method'];
            $daemon->Name             = $parameters['title'];
            $daemon->MaxExecutionTime = $parameters['maxExecutionTime'];
            $daemon->StartDate        = $parameters['startDate'];
            $daemon->EndDate          = $parameters['endDate'];
            $daemon->Active           = $parameters['active'];
            $daemon->Params           = $parameters['params'];

            if ( $daemon->Active === null ) {
                $daemon->Active = true;
            }

            if ( $daemon->MaxExecutionTime == null ) {
                $daemon->MaxExecutionTime = '00:03:00';
            }

            return $daemon;
        }


        /**
         * Can Run?
         *
         * @return bool
         */
        public function CanRun() {
            if ( !$this->Active ) {
                return false;
            }

            $now = DateTimeWrapper::Now();

            if ( $this->StartDate ) {
                if ( $now <= Convert::ToDateTime( $this->StartDate ) ) {
                    return false;
                }
            }

            if ( $this->EndDate ) {
                if ( $now >= Convert::ToDateTime( $this->EndDate ) ) {
                    return false;
                }
            }

            return true;
        }


        /**
         * Get Method Name
         *
         * @return string
         */
        public function GetMethodName() {
            if ( is_array( $this->Method ) ) {
                list( $m, $n ) = $this->Method;

                return $m . '::' . $n;
            } else {
                return $this->Method;
            }
        }


        /**
         * Get Daemon Lock
         *
         * @return DaemonLock
         */
        public function GetDaemonLock() {
            $daemonLock                   = new DaemonLock();
            $daemonLock->maxExecutionTime = $this->MaxExecutionTime;
            $daemonLock->methodName       = $this->GetMethodName();
            $daemonLock->packageName      = $this->Package;
            $daemonLock->title            = $this->Name;
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
                    Logger::Info( "Lock {$lock->title} is active" );
                    return false;
                } else {
                    Logger::Warning( "Flushing inactive lock {$lock->title}" );
                    DaemonLockFactory::Delete( $lock );
                }
            }

            $result = DaemonLockFactory::Add( $this->GetDaemonLock() );
            Logger::Info( "Locked {$this->Name}, %s", $result );

            return true;
        }


        /**
         * Unlock
         * @return bool
         */
        public function Unlock() {
            $lock = $this->CheckLock();
            if ( $lock ) {
                return DaemonLockFactory::Delete( $lock );
            }

            return true;
        }


        /**
         * Check Lock
         *
         * @return DaemonLock
         */
        public function CheckLock() {
            $dl   = $this->GetDaemonLock();
            $lock = DaemonLockFactory::GetOne(
                [
                    "packageName"  => $dl->packageName
                    , "methodName" => $dl->methodName
                    , "title"      => $dl->title
                ]
            );

            return $lock;
        }


        /**
         * Run Daemon
         *
         */
        public function Run() {
            if ( !$this->CanRun() ) {
                Logger::Info( "{$this->Name} couldn't be run." );
                return false;
            }

            set_time_limit( 0 );
            if ( !$this->Lock() ) {
                Logger::Warning( "Failed to lock {$this->Name}" );
                return false;
            }

            try {
                call_user_func_array( $this->Method, [ $this->Params ] );
            } catch ( Exception $e ) {
                Logger::Error( "{$this->Name}: exception {$e->getMessage()}" );
            }

            $this->Unlock();

            return true;
        }
    }
