<?php
    /**
     * Locale Loader
     *
     * @desc Module Parameters
     * senderEmail: sender email
     * senderName:  sender name
     * charset:     encoding, default is iso-8859-1
     * xMailer:     xmailer, default is Eaze
     * @package Eaze
     * @subpackage Eaze.Modules
     * @static
     * @author Sergey Bykov
     */
    class MailFactory implements IModule {
        /**
         * Initialized Flag
         *
         * @var boolean
         */
        public static $Initialized = false;

        /**
         * Module Parameters
         *
         * @var array
         */
        private static $params = array();

        /** Charset of message */
        private $charset           = "iso-8859-1";

        /** Content Type */
        private $contentType       = "text/html";

        /** Encoding */
        private $encoding          = "8bit";

        /** Mime Version */
        private $mimeVersion       = "1.0";

        /** Content Type */
        private $msgContentType    = "multipart/mixed; boundary=\"Message-Boundary\"";

        /** X-mailer */
        private $xMailer           = "Eaze";

        /** Sender email */
        private $senderEmail       = "";

        /** Sender Name */
        private $senderName        = "";

        /** Subject */
        private $subject           = "";

        /** Headers */
        private $headers           = "";

        /** Body */
        private $body              = "";

        /** Message Body */
        private $messageBody       = "";

        /** Sets word wrapping on the body of the message to a given number of characters */
        private $wordWrap          = 0;

        /** To */
        private $to                = array();

        /** CC */
        private $cc                = array();

        /** BCC */
        private $bcc               = array();

        /** Reply To */
        private $replyTo           = array();

        /** Attachments */
        private $attachment        = array();

        /** LE */
        private $EOL               = "\n";

        /** Error Strings </p> */
        public static $Errors = array(
            1   => "Sender email is null."
            , 2 => "Sender email is not valid."
            , 3 => "There are no valid recepients."
            , 4 => "Function \"mail\" does not exists."
        );


        /**
         * Switches between html and plain text modes
         *
         * @param boolena $isHTML  the html mode if<code>true</code>.
         */
        public function SetHTML( /*boolean*/ $isHTML ) {
            if ( true == $isHTML ) {
                $this->contentType = "text/html";
            } else {
                $this->contentType = "text/plain";
            }
        }


        /**
         * Add Recipient ( "to" ) address
         *
         * @param string $email  the user email
         * @param string $name   the user name
         */
        public function AddRecipient( /*string*/ $email, /*string*/ $name = null ) {
            $this->to[] = array(
                "email"  => $email
                , "name" => $name
            );
        }


        /**
         * Add "CC" adress
         *
         * @param string $email  the user email
         * @param string $name   the user name
         */
        public function AddCC( /*string*/ $email, /*string*/ $name = null ) {
            $this->cc[] = array(
                "email"  => $email
                , "name" => $name
            );
        }


        /**
         * Add "to" adress
         *
         * @param string $email  the user email
         * @param string $name   the user name
         */
        public function AddBCC( /*string*/ $email, /*string*/ $name = null ) {
            $this->bcc[] = array(
                "email"  => $email
                , "name" => $name
            );
        }


        /**
         * Set "ReplyTo" adress
         *
         * @param string $email  the user email
         * @param string $name   the user name
         */
        public function AddReplyTo( /*string*/ $email, /*string*/ $name = null ) {
            $this->replyTo[] = array(
                "email"  => $email
                , "name" => $name
            );
        }


        /**
         * Add Attachments
         *
         * @param string $filename     the file
         * @param string $name         the filename
         * @param string $content      the file content
         * @param string $contentType  the content type
         */
        public function AddAttachment( /*string*/ $filename
                                      , /*string*/ $name = null
                                      , /*string*/ $content = null
                                      , /*string*/ $contentType = null ) {
            $this->attachment[] = array(
                "file"          => $filename
                , "filename"    => $name
                , "content"     => $content
                , "contentType" => $contentType
            );
        }


        /**
         * Add Hedaer
         *
         * @param string $header  the header
         * @param string $value   the hedaer value
         */
        public function AddHeader( $header, $value ) {
            $this->headers .= $header . ": " . $value . $this->EOL;
        }


        /**
         * Set xMailer
         *
         * @param string $xMailer  the xMailer
         */
        public function SetXMailer( /*string*/ $xMailer ) {
            $this->xMailer = $xMailer;
        }


        /**
         * Set Message Charset
         *
         * @param string $charset  the message charset
         */
        public function SetCharset( /*string*/ $charset ) {
            $this->charset = $charset;
        }


        /**
         * Add Body
         *
         * @param string $header  the header
         * @param string $value   the hedaer value
         */
        public function AddBody( /*string*/ $header, /*string*/ $value = null ) {
            if ( true == empty( $value ) ) {
                $this->body .= $header . $this->EOL;
            } else {
                $this->body .= $header . ": " . $value . $this->EOL;
            }
        }


        /** Clear Recipients ("to") */
        public function ClearRecipients() {
            $this->to = array();
        }


        /** Clear CCs */
        public function ClearCCs() {
            $this->cc = array();
        }


        /** Clear BCCs */
        public function ClearBCCs() {
            $this->to = array();
        }


        /** Clear ReplyTos */
        public function ClearReplyTos() {
            $this->replyTo = array();
        }


        /** Clear All Recipients */
        public function ClearAllRecipients() {
            $this->clearRecipients();
            $this->clearCCs();
            $this->clearBCCs();
        }


        /** Clear Attachments */
        public function ClearAttachments() {
            $this->attachment = array();
        }


        /** Set sender name and email */
        public function SetSender( /*string*/ $email, /*string*/ $name = null ) {
            $this->senderEmail = $email;
            $this->senderName  = $name;
        }


        /** Set subject */
        public function SetSubject( /*string*/ $subject ) {
            $this->subject = $subject;
        }

        /** Set wordwrap */
        public function SetWordwrap( /*integer*/ $wordwrap ) {
            $this->wordWrap = $wordwrap;
        }


        /** Get subject */
        public function GetSubject() {
            return ( stripslashes( $this->subject ) );
        }


        /** Set message body */
        public function SetMessageBody( /*string*/ $messageBody ) {
            $this->messageBody = $messageBody;
        }


        /** Get Body */
        public function GetBody() {
            return $this->body;
        }


        /** Get Sender String */
        public function GetSenderString() {
            $senderString = $this->senderEmail;

            if ( false == empty( $this->senderName ) ) {
                $senderString = "\"" . $this->senderName . "\" " . "<" . $this->senderEmail . ">" ;
            }

            return ( $senderString );
        }

        /**
         * Set email content type
         * @param $contentType
         * @return void
         */
        public function SetMsgContentType( $contentType ) {
           $this->msgContentType = ! empty( $contentType ) ? $contentType : $this->msgContentType;
        }


        /**
         *  Check email format
         *
         * @param string $email
         * @return boolean
         */
        public static function CheckEmailFormat( /*string*/ $email ) {
            if ( true == preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$/', $email ) ) {
                return ( true );
            } else {
                return ( false );
            }
        }


        /**
         * Check MX Host
         *
         * @param string $email
         * @return bool
         */
        public static function CheckMXHost( /*string*/ $email ) {
            if (!self::CheckEmailFormat( $email )) {
                return false;
            }

        	$userDomain =  explode("@", $email);
        	if ( true == isset( $userDomain[1] )) {
        	    if ( function_exists("getmxrr")) {
        	        $hostArray = null;
                    getmxrr( $userDomain[1], $hostArray );
        	    } else {
        	        $hostArray  = gethostbynamel( $userDomain[1] );
        	    }
            } else {
                return false;
            }

            if ( (true == is_array( $hostArray )) && (count( $hostArray ) > 0) ) {
                return true;
            }

            return false;
        }


        /**
         * Get Adresses String
         *
         * @param array $adresses  the array of emails and names
         * @return string
         */
        public function GetAdressesString( /*array*/ $adresses ) {
            $i           = 1;
            $newAdresses = array();
            $adressesStr = "";

            // check for incorrect email or empty emails
            foreach ( $adresses as $adress ) {
                if ( false == empty( $adress["email"] ) ) {

                    if ( true == $this->checkEmailFormat( $adress["email"] ) ) {

                        if ( true == empty( $adress["name"] ) ) {
                            $newAdresses[] = $adress["email"];
                        } else {
                            $newAdresses[] = "\"" . $adress["name"] . "\" <" . $adress["email"] . ">";
                        }
                    }
                }
            }

            //set adresses string
            foreach ( $newAdresses as $adress ) {
                $adressesStr .= $adress;

                if ( $i != count( $newAdresses ) ) {
                    $adressesStr .= ", ";

                    $i++;
                }
            }

            return ( $adressesStr );
        }


        /** Proccess plain text word wrap */
        private function proccessWordWrap( $wordwrap = 0 ) {
            $this->wordWrap = $wordwrap;

            if (( $this->wordWrap >= 20 )
                    && ( "text/plain" == $this->contentType )) {
                $this->messageBody = wordwrap( $this->messageBody, $this->wordWrap, "\n" );
            }
        }


        /**
         * Send Mail Function
         *
         * @param bool $showMail  show mail message
         */
        public function SendMail( $showMail = false ) {
            $errorCodes = $this->validate();

            //check for right data
            if ( false == empty( $errorCodes ) ) {
                return ( $errorCodes );
            }

            //formig headers
            $this->addHeader( "From",         $this->getSenderString() );
            $this->addHeader( "Reply-To",     $this->getAdressesString( $this->replyTo ));

            if ( "" != trim( $this->getAdressesString( $this->cc ) ) ) {
                $this->addHeader( "CC", $this->getAdressesString( $this->cc ));
            }

            if ( "" != trim( $this->getAdressesString( $this->bcc ) ) ) {
                $this->addHeader( "BCC", $this->getAdressesString( $this->bcc ));
            }

            $this->addHeader( "X-Mailer",     $this->xMailer );
            $this->addHeader( "MIME-version", $this->mimeVersion );
            $this->addHeader( "Content-type", $this->msgContentType );

            //form body headers
            $this->addBody( "--Message-Boundary" );
            $this->addBody( "Content-type",              $this->contentType . "; charset=\"" . $this->charset . "\"" );
            $this->addBody( "Content-transfer-encoding", $this->encoding . "\n" );
            //$this->addBody( "\n" );


            $this->proccessWordWrap( $this->wordWrap );

            $this->addBody( $this->messageBody );
            $this->proccessAttachments();

            //return true;

            if (( true == function_exists( "mail" ) )
                    && ( $showMail == false )) {
                $result = mail(
                    $this->getAdressesString( $this->to )
                    , $this->getSubject()
                    , $this->getBody()
                    , $this->headers
                    , "-f" . $this->senderEmail
                );

                return ( $result );
            } elseif ( $showMail == true ) {
                $message = "To: " . $this->getAdressesString( $this->to ) . $this->EOL
                        . "Subject: " . $this->getSubject() . $this->EOL
                        . $this->headers . $this->EOL
                        . $this->getBody();

                return ( $message );
            }

            return false;
        }


        /**  Proccess Attachments */
        private function proccessAttachments() {
            if  ( false == empty( $this->attachment ) ) {

                foreach ( $this->attachment as $file ) {

                    if ( true == file_exists( $file["file"] ) ) {
                        $content = file_get_contents( $file["file"] );
                    } elseif ( false == is_null( $file["content"] ) ) {
                        $content = $file["content"];
                    } else {
                        continue;
                    }

                    $encodedAttach = chunk_split( base64_encode( $content ) );

                    $this->addBody( "\n\n--Message-Boundary" );

                    if ( true == empty( $file["filename"] ) ) {
                        $file["filename"] = basename( $file["file"] );
                    }

                    $this->addBody( "Content-Disposition",       "attachment; filename=\"" . $file["filename"] . "\"" );
                    if ( true == empty( $file["contentType"] ) ) {
                        $this->addBody( "Content-Type", "application/octet-stream; name=\"" . $file["filename"] . "\"" );
                    } else {
                        $this->addBody( "Content-Type", $file["contentType"] . "; name=\"" . $file["filename"] . "\"" );
                    }
                    $this->addBody( "Content-ID", "<" . $file["filename"] . ">" );
                    $this->addBody( "Content-Transfer-Encoding", "base64\n" );
                    $this->addBody( $encodedAttach );
                }
                $this->addBody( "--Message-Boundary--" );
            }
        }


        /**
         *  Constructor
         *
         * @param string $senderEmail  the sender email
         * @param string $senderName   the sender name
         * @param string $charset      the charset
         * @param string $xMailer      the xMailer
         * @param string $subject      the subject
         * @param string $messageBody  the message body
         */
        public function __construct( /*string*/$senderEmail = null
                                    , /*string*/ $senderName = null
                                    , /*string*/ $charset = null
                                    , /*string*/ $xMailer = null
                                    , /*string*/ $subject = null
                                    , /*string*/ $messageBody = null ) {
            if ( false == is_null( $xMailer ) ) {
                $this->setXMailer( $xMailer );
            }

            if ( false == is_null( $charset ) ) {
                $this->setCharset( $charset );
            }

            $this->setSender( $senderEmail, $senderName );
            $this->setSubject( $subject );
            $this->setMessageBody( $messageBody );
        }


        /** Validate data */
        public function Validate() {
            $errorCodes = array();

            if ( true == empty( $this->senderEmail ) ) {
                $errorCodes[] = 1;
            }

            if ( false == $this->checkEmailFormat( $this->senderEmail ) ) {
                $errorCodes[] = 2;
            }

            if ( "" == trim( $this->getAdressesString( $this->to )) ) {
                $errorCodes[] = 3;
            }

            if ( true == empty( $this->replyTo ) ) {
                $this->addReplyTo( $this->senderEmail, $this->senderName );
            }

            return ( $errorCodes );
        }


        /**
         * Init Module
         *
         * @param DOMNodeList $params  the params node list
         * @static
         */
        public static function Init(DOMNodeList $params) {
            foreach ( $params as $param ) {
                /** @var DOMElement $param */
                self::$params[$param->getAttribute("name")] = $param->nodeValue;
            }

            if ( !isset( self::$params["senderEmail"] ) ) {
                self::$params["senderEmail"] = "";
            }

            if ( !isset( self::$params["senderName"] ) ) {
                self::$params["senderName"] = "";
            }

            if ( !isset( self::$params["charset"] ) ) {
                self::$params["charset"] = "iso-8859-1";
            }

            if ( !isset( self::$params["bcc"] ) ) {
                self::$params["bcc"] = "";
            }

            if ( !isset( self::$params["xMailer"] ) ) {
                self::$params["xMailer"] = "Eaze v1.0";
            }

            self::$Initialized = true;
        }


        /**
         * Constructs an object
         *
         * @return MailFactory
         */
        public static function Get() {
            if ( !self::$Initialized ) {
                return null;
            }

            $mf = new MailFactory( self::$params["senderEmail"]
                    , self::$params["senderName"]
                    , self::$params["charset"]
                    , self::$params["xMailer"]
            );

            if ( !empty( self::$params["bcc"] ) ) {
                $mf->AddBCC( self::$params["bcc"] );
            }

            return $mf;
        }
    }
?>