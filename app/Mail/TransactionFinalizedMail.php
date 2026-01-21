<?php

namespace App\Mail;

use App\Models\SaleTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TransactionFinalizedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;

    /**
     * Create a new message instance.
     */
    public function __construct(SaleTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Transaction Finalized - Invoice #' . $this->transaction->invoice_no,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.transaction-finalized',
            with: [
                'transaction' => $this->transaction,
                'customer' => $this->transaction->customer,
                'items' => $this->transaction->items,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        try {
            // Find receipt image path
            $receiptImagePath = null;
            
            if ($this->transaction->receipt_path) {
                $receiptPath = $this->transaction->receipt_path;
                
                \Log::info('Looking for receipt', ['receipt_path' => $receiptPath]);
                
                // Try different path formats
                $pathsToTry = [
                    // If path starts with 'storage/', it's in public/storage (symlinked)
                    public_path($receiptPath),
                    // Or in storage/app/public
                    storage_path('app/public/' . str_replace('storage/', '', $receiptPath)),
                    // Direct storage/app path
                    storage_path('app/' . $receiptPath),
                    // Just the filename in receipts folder
                    storage_path('app/public/receipts/' . basename($receiptPath)),
                ];
                
                foreach ($pathsToTry as $path) {
                    if (file_exists($path)) {
                        $receiptImagePath = $path;
                        \Log::info('Receipt found for PDF', [
                            'path' => $path,
                            'size' => filesize($path) . ' bytes'
                        ]);
                        break;
                    }
                }
                
                if (!$receiptImagePath) {
                    \Log::warning('Receipt not found', [
                        'receipt_path' => $receiptPath,
                        'tried_paths' => $pathsToTry
                    ]);
                }
            }
            
            // Generate PDF with invoice and receipt
            $pdf = \PDF::loadView('pdf.invoice-with-receipt', [
                'transaction' => $this->transaction,
                'receiptImagePath' => $receiptImagePath
            ]);
            
            // Set PDF options
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans'
            ]);
            
            // Save PDF to temporary file
            $pdfPath = storage_path('app/temp/invoice_' . $this->transaction->invoice_no . '_' . time() . '.pdf');
            
            // Create temp directory if it doesn't exist
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            // Save PDF
            $pdf->save($pdfPath);
            
            \Log::info('PDF generated successfully', [
                'invoice_no' => $this->transaction->invoice_no,
                'pdf_path' => $pdfPath,
                'has_receipt' => $receiptImagePath !== null
            ]);
            
            // Attach PDF
            $attachments[] = Attachment::fromPath($pdfPath)
                ->as('Invoice_' . $this->transaction->invoice_no . '.pdf')
                ->withMime('application/pdf');
            
        } catch (\Exception $e) {
            \Log::error('Failed to generate PDF attachment', [
                'error' => $e->getMessage(),
                'invoice_no' => $this->transaction->invoice_no,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback: Try to attach just the receipt image if PDF fails
            if ($this->transaction->receipt_path) {
                $receiptPath = $this->transaction->receipt_path;
                $pathsToTry = [
                    storage_path('app/public/receipts/' . basename($receiptPath)),
                    storage_path('app/public/' . str_replace('storage/', '', $receiptPath)),
                ];
                
                foreach ($pathsToTry as $path) {
                    if (file_exists($path)) {
                        $attachments[] = Attachment::fromPath($path)
                            ->as('receipt_' . $this->transaction->invoice_no . '.' . pathinfo($path, PATHINFO_EXTENSION))
                            ->withMime(mime_content_type($path));
                        break;
                    }
                }
            }
        }

        return $attachments;
    }
}
