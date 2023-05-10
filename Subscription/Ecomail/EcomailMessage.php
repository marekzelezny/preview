<?php

namespace App\Extensions\Ecomail;

use Illuminate\Notifications\Messages\MailMessage;

class EcomailMessage extends MailMessage
{
    public function __construct(protected array $tos = [])
    {
    }

    public function sendTo(string $email, string $name = null): self
    {
        $this->tos[] = [
            'email' => $email,
            'name' => $name,
        ];

        return $this;
    }

    public function addAttachment(string $file): self
    {
        $this->attachments[] = [
            'type' => 'mime/type',
            'name' => 'Příloha.pdf',
            'content' => base64_encode($file),
        ];

        return $this;
    }

     public function structure(): array
     {
         return [
             'template_id' => $this->view,
             'subject' => $this->subject,
             'from_email' => config('mail.from.address'),
             'from_name' => config('mail.from.name'),
             'to' => $this->tos,
             'global_merge_vars' => $this->mapGlobalVars(),
             'attachments' => $this->attachments,
         ];
     }

     protected function mapGlobalVars(): array
     {
         return array_map(function ($value, $key) {
             return ['name' => $key, 'content' => (string) $value];
         }, $this->viewData, array_keys($this->viewData));
     }
}
