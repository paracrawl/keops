<?php
    require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/mail_template.interface.php");
    class MailTemplate implements MailTemplateI {
        public function getSubject($params = null) {
            return "You have been invited to KEOPS!";
        }

        public function getHead($params = null) {
            return 
            <<<HTML
                    <style>
            body {
                margin: 0;
                font-family: sans-serif, serif;
            }

            .container {
                width: 70%;
                margin: auto;
            }

            .header {
                background-color: #004a7a;
            }

            .header .brand {
                width: 70%;
                margin: auto;
            }

            .header .brand img {
                vertical-align: middle;
                display: inline-block;
            }

            .header .brand .brand-text {
                vertical-align: middle;
                display: inline-block;
                color: white;
            }

            .body {
                padding-top: 16px;
            }

            .well {
                background-color: #f5f5f5;
                padding: 16px;
                border-radius: 5px 5px 5px 5px;
            }

            .footer {
                border-top: solid 1px #f5f5f5;
                padding-top: 16px;
            }
        </style>
HTML;
        }

        public function getBody($params = null) {
            return 
            <<<HTML
        <div class="header">
            <div class="brand container">
                <img alt="" src="https://keops.prompsit.com/img/tiny-pyramids-white.png"  />
                <span class="brand-text">KEOPS</span>
            </div>
        </div>

        <div class="container">
            <div class="body">
                You have been invited to join KEOPS! Please follow this link to do so:

                <p class="well"><a href="{$params->token_url}">{$params->token_url}</a></p>
            </div>

            <div class="footer">
                Keops by Prompsit Language Engineering  | <a href="http://www.prompsit.com/home/">www.prompsit.com</a>
            </div>
        </div>
HTML;
        }
    }
?>