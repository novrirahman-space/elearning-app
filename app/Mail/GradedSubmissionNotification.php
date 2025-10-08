<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GradedSubmissionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Submission $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function build()
    {
        return $this->subject('Tugas Kamu Telah Dinilai')
            ->view('emails.graded-submission')
            ->with([
                'studentName' => $this->submission->student->name,
                'assignmentTitle' => $this->submission->assignment->title,
                'courseName' => $this->submission->assignment->course->name,
                'score' => $this->submission->score
            ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Graded Submission Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.graded-submission',
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
