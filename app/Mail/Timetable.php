<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class Timetable extends Mailable
{
    use Queueable, SerializesModels;

    protected Collection $timetableEvents;
    protected Carbon $startDate;
    protected Carbon $endDate;

    /**
     * Create a new message instance.
     */
    public function __construct(Collection $timetableEvents, Carbon $startDate, Carbon $endDate)
    {
        $this->timetableEvents = $timetableEvents;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Timetable',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.timetable',
            with: [
                'timetableEvents' => $this->timetableEvents,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
