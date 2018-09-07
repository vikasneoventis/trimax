<?php
/**
 * @author Evince Team
 * @copyright Copyright © 2018 Evince (http://evincemage.com/)
 */

namespace Evincemage\MailLogger\Plugin\Mail\Transport;

use Magento\Framework\Mail\TransportInterface;
use Evincemage\MailLogger\Helper\Data;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Phrase;

class OnSendMessage
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * OnSendMessage constructor.
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    )
    {
        $this->helper = $helper;
    }

    /**
     * @param TransportInterface $subject
     * @param \Closure $proceed
     * @throws MailException
     */
    public function aroundSendMessage(TransportInterface $subject, \Closure $proceed)
    {
        try {
            $result = $proceed();

            // Successful email sending log
            if ($this->helper->isEnabled()) {
                $this->emailLog($subject);
            }

            //return $result;
        } catch (\Exception $e) {

            // Failed email log
            if ($this->helper->isEnabled()) {
                $this->emailLog($subject);
            }

            throw new MailException(new Phrase($e->getMessage()), $e);
        }
    }

    /**
     * @param TransportInterface $subject
     * @return null
     */
    protected function emailLog(TransportInterface $subject)
    {
        try {

            if (method_exists($subject, 'getMessage')) {
                $message = $subject->getMessage();
            }else {
                $reflection = new \ReflectionClass($subject);
                $_message = $reflection->getProperty('_message');

                $_message->setAccessible(true);

                /* @var $message \Magento\Framework\Mail\Message */
                $message = $_message->getValue($subject);
            }

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/allEmail.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info(print_r(json_encode([
                'headers' => $message->getHeaders(),
                'from' => $message->getFrom(),
                'recipients' => $message->getRecipients(),
                'subject' => $message->getSubject(),
                'reply_to' => $message->getReplyTo(),
                'return_path' => $message->getReturnPath(),
                'charset' => $message->getCharset(),
            ], JSON_PRETTY_PRINT), true));

        } catch (\Throwable $t) {
            return null;
        }
    }


}