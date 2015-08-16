<?php
    use Eaze\Core\Request;
    use Eaze\Core\Response;
    use Eaze\Helpers\SecureTokenHelper;
    use Eaze\Site\Page;

    class GetStaticPage {

        /**
         * Execute GetStaticPage
         */
        public function Execute() {
            $with404 = Request::GetBoolean( 'gsp_With404' );
            $url     = Request::GetString( 'gsp_Url' );
            $success = Request::GetString( 'success' );
            $url     = $url ?: Page::$RequestData[0];
            $page    = StaticPageFactory::GetOne( [ 'url' => $url, 'statusId' => 1 ] );

            if ( !$page && $with404 ) {
                Response::HttpStatusCode( 404 );
            }

            if ( $page ) {
                $page->images = ObjectImageFactory::Get( [ 'objectId' => $page->staticPageId, 'objectClass' => get_class( $page ) ] );
            }

            // form logic
            $errors = [ ];
            $form   = OrderForm::GetFromRequest();
            if ( SecureTokenHelper::Check() ) {
                $errors = $form->Validate();
                if ( !$errors ) {
                    if ( !$form->Send( OrderForm::GetEmails() ) ) {
                        $errors['mail'] = 'failed';
                    } else {
                        Response::SetParameter( 'url', $url );
                        return 'url';
                    }
                }
            }

            $success = $success === '';

            Context::SetObject( $page );
            Context::AddBreadcrumb( $page->title );

            Response::SetParameter( '__page', $page );
            Response::SetParameter( 'form', $form );
            Response::SetArray( 'errors', $errors );
            Response::SetBoolean( 'success', $success );
        }
    }
