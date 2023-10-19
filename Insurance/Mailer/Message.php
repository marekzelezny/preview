<?php

namespace Adity\Mailer;

class Message
{
    protected array $to = [];
    protected array $additional_recipients = [];
    protected array $headers = [];
    protected string $subject;
    protected string $message;
    protected string $template = 'default';
    protected array $content;

    public static function create() : self
    {
        return new self();
    }

    public function to(string|array $to) : self
    {
        if (is_array($to)) {
            array_push($this->to, ...$to);
        } else {
            array_push($this->to, $to);
        }

        return $this;
    }

    public function bcc(array $recipients) : self
    {
        $this->additional_recipients = $recipients;

        foreach ($this->additional_recipients as $recipient) {
            $email = $recipient['email'];
            $this->headers[] = "Bcc: $email";
        }

        return $this;
    }

    public function subject(string $subject) : self
    {
        $this->subject = $subject;

        return $this;
    }

    public function message(string $message) : self
    {
        $this->message = $message;

        return $this;
    }

    public function template(string $template) : self
    {
        $this->template = $template;

        return $this;
    }

    public function content(array $content) : self
    {
        $this->content = $content;

        return $this;
    }

    public function send() : void
    {
        $message = $this->mergeStringWithContent(
            $this->message
        );

        $subject = $this->mergeStringWithContent(
            $this->subject
        );

        $template = new Template(
            view: $this->template,
            with: [
                'message' => $message,
            ]
        );

        wp_mail(
            to: $this->to,
            subject: $subject,
            message: $template->render(),
            headers: $this->headers
        );
    }

    public function mergeStringWithContent(string $string) : string
    {
        foreach ($this->content as $key => $value) {
            $string = str_replace("[$key]", $value, $string);
        }

        return $string;
    }
}
