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
        $documents = DB::table('documents')->get(); // Fetch all documents from DB

        return view('admin.documents', compact('documents')); // Pass data to the view
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
            'uploaded_by' => auth()->user()->name, // Store user ID instead of name
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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

        // Delete file from storage
        Storage::delete('public/' . $document->file_path);

        // Remove record from the database
        DB::table('documents')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }

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
            return response()->file($filePath, [
                'Content-Type' => mime_content_type($filePath),
            ]);
        }

        // If the file does not exist, return a 404 error
        abort(404, 'File not found.');
    }
}
