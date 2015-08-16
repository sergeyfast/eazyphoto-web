<?php
    use Eaze\Core\Request;
    use Eaze\Modules\MailFactory;
    use Eaze\Site\Host;


    /**
     * Order Form
     * @package    EazyPhoto
     * @subpackage Size
     */
    class OrderForm {

        /**
         * @var string
         */
        public $Name;


        /**
         * @var string
         */
        public $Email;

        /**
         * @var string
         */
        public $Phone;

        /**
         * @var string
         */
        public $Comment;


        /**
         * Get From Request
         * @param string $prefix
         * @return OrderForm
         */
        public static function GetFromRequest( $prefix = 'form' ) {
            $of   = new OrderForm();
            $form = Request::GetArray( $prefix ) ?: [ ];
            foreach ( [ 'Name', 'Email', 'Phone', 'Comment' ] as $key ) {
                $of->$key = \Eaze\Helpers\ArrayHelper::GetValue( $form, strtolower( $key ) );
            }

            return $of;
        }


        /**
         * Validate form
         * @return string[]
         */
        public function Validate() {
            $errors = [ ];

            if ( !$this->Name ) {
                $errors['name'] = 'empty';
            }

            if ( !$this->Email && !$this->Phone ) {
                if ( !$this->Email ) {
                    $errors['email'] = 'empty';
                }

                if ( !$this->Phone ) {
                    $errors['phone'] = 'empty';
                }
            }

            if ( $this->Email && !MailFactory::CheckEmailFormat( $this->Email ) ) {
                $errors['email'] = 'format';
            }

            return $errors;
        }


        /**
         * Format email subject
         * @param $subject
         * @return string
         */
        private static function getSubject( $subject ) {
            return ( '=?utf-8?b?' . base64_encode( $subject ) . '?=' );
        }


        /**
         * Send Message
         * @param string[] $emails
         * @return array|bool|string
         */
        public function Send( $emails ) {
            if ( !$emails ) {
                return false;
            }

            $message = <<<html
            <h3>Новая заявка</h3>
            <p><strong>{$this->Name}</strong>, {$this->Phone} {$this->Email}</p>
            <p>{$this->Comment}</p>
html;

            $mailFactory = MailFactory::Get();

            foreach ( $emails as $email ) {
                $mailFactory->AddRecipient( $email );
            }

            $mailFactory->SetSubject( self::getSubject( 'Новая заявка с сайта ' . Host::GetCurrentHost()->GetHostname() ) );
            $mailFactory->SetHTML( true );
            $mailFactory->SetMessageBody( $message );

            return $mailFactory->SendMail();
        }


        /**
         * Get Emails
         * @return array
         */
        public static function GetEmails() {
            $sph = new SiteParamHelper();
            if ( !$sph->HasEmail() ) {
                return [ ];
            }

            return explode( ' ', $sph->GetEmail() );
        }
    }