<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a list of documents.
     */
    public function index()
    {
        $documents = DB::table('documents')
            ->where('status', 'Active')
            ->get();

        return view('admin.documents', [
            'documents' => $documents,
            'isArchive' => false
        ]);
    }

    /**
     * Handle file upload and store details in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|mimes:pdf,doc,docx,png,jpg,jpeg|max:51200' // Allow up to 50MB
        ]);

        $file = $request->file('file');
        $filePath = $file->store('documents', 'public');

        DB::table('documents')->insert([
            'title' => $request->title,
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'status' => 'Active', // Default status
            'uploaded_by' => auth()->user()->id, // Store user ID instead of name
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Log upload action
        $this->logDocumentHistory(auth()->id(), $request->title, 'uploaded');

        return redirect()->back()->with('success', 'Document uploaded successfully!');
    }

    /**
     * Download the specified document.
     */
    public function download($id)
    {
        $document = DB::table('documents')->where('id', $id)->first();

        if (!$document) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        $filePath = $document->file_path; // e.g., "documents/filename.jpg"

        // Check if file exists
        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on the server.');
        }

        // Log download action
        $this->logDocumentHistory(auth()->id(), $document->title, 'downloaded');

        return Storage::disk('public')->download($filePath, $document->title . '.' . $document->file_type);
    }

    /**
     * Delete a document from storage and database.
     */
    public function delete($id)
    {
        $document = DB::table('documents')->where('id', $id)->first();

        if (!$document) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        // Instead of deleting, update status to 'Inactive'
        DB::table('documents')
            ->where('id', $id)
            ->update([
                'status' => 'Inactive',
                'updated_at' => now()
            ]);

        // Log status change action
        $this->logDocumentHistory(auth()->id(), $document->title, 'status changed to Inactive');

        return redirect()->back()->with('success', 'Document status changed to Inactive successfully.');
    }

    /**
     * View a document.
     */
    public function view($id)
    {
        // Fetch the document using the DB facade
        $document = DB::table('documents')->where('id', $id)->first();

        // Check if the document exists
        if (!$document) {
            abort(404, 'Document not found.');
        }

        // Correct storage path
        $filePath = storage_path('app/public/' . $document->file_path);

        // Check if the file exists
        if (file_exists($filePath)) {
            $this->logDocumentHistory(auth()->id(), $document->title, 'viewed');

            return response()->file($filePath, [
                'Content-Type' => mime_content_type($filePath),

            ]);
        }

        // If the file does not exist, return a 404 error
        abort(404, 'File not found.');
    }

    /**
     * Retrieve document history.
     */
    public function getDocumentHistory()
    {
        $history = DB::table('document_history')
            ->join('users', 'document_history.user_id', '=', 'users.id')
            ->select('users.name as user', 'document_history.action', 'document_history.file_name', 'document_history.timestamp')
            ->orderBy('document_history.timestamp', 'desc')
            ->get();

        return response()->json($history);
    }


    /**
     * Log document actions (upload, delete, download).
     */
    private function logDocumentHistory($userId, $fileName, $action)
    {
        $user = DB::table('users')->where('id', $userId)->first();

        if (!$user) {
            return;
        }

        // Friendly message based on action
        $actionMessages = [
            'uploaded' => "{$user->name} uploaded a new document: <strong>{$fileName}</strong>.",
            'deleted' => "{$user->name} deleted the document: <strong>{$fileName}</strong>.",
            'downloaded' => "{$user->name} downloaded the document: <strong>{$fileName}</strong>.",
            'viewed' => "{$user->name} viewed the document: <strong>{$fileName}</strong>."
        ];

        $message = $actionMessages[$action] ?? "{$user->name} performed an action on <strong>{$fileName}</strong>.";

        DB::table('document_history')->insert([
            'user_id' => $userId,
            'file_name' => $fileName,
            'action' => $message, // Store the friendly message
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle document archives.
     */
    public function archives()
    {
        $documents = DB::table('documents')
            ->where('status', 'Inactive')
            ->get();

        return view('admin.documents', [
            'documents' => $documents,
            'isArchive' => true
        ]);
    }

    /**
     * Handle document restoration.
     */

    public function restore($id)
    {
        $document = DB::table('documents')->where('id', $id)->first();

        if (!$document) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        DB::table('documents')
            ->where('id', $id)
            ->update([
                'status' => 'Active',
                'updated_at' => now()
            ]);

        $this->logDocumentHistory(auth()->id(), $document->title, 'restored from archive');

        return redirect()->route('documents.archives')->with('success', 'Document restored successfully.');
    }
}
