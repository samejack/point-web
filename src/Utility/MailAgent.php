<?php

namespace point\web;

class Utility_MailAgent
{
    /**
     * Use utf-8 encoding to send mail
     * 
     * @param string $subject
     * @param string $mailForm
     * @param string $mailTo
     * @param string $mailText
     * @param string $sender
     * @return boolean send status
     */
    public function send($subject, $mailForm, $mailTo, $mailText, $sender=null)
    {
        if ($sender === null) {
            $sender = 'PRSP Mail Agent';
        }
        $subject = $this->_convertHeaderString($subject);
        $senderText = $this->_convertHeaderString($sender)."<$mailForm>";
        $headers  = "Content-Type: text/html; charset=utf-8\r\n";
        $headers .= "From: $senderText\r\n";
        $headers .= "Reply-To: $senderText\r\n";
        $headers .= "Return-Path: $senderText\r\n";
        $headers .= "X-Mailer: PHP\r\n";
        if (mail($mailTo, $subject, $mailText, $headers, '-f' . $mailTo)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Convert header string fro RFC 2047
     * 
     * @param string $string
     * @return string
     */
    private function _convertHeaderString($string)
    {
        return sprintf('=?UTF-8?B?%s?=', base64_encode($string));
    }
}