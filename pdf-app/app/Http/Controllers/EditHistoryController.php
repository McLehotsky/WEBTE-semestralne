<?php

namespace App\Http\Controllers;

use App\Models\EditHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\PdfEdit;


class EditHistoryController extends Controller
{
    public function index()
    {
        $logs = EditHistory::with(['user', 'pdfEdit'])->orderByDesc('used_at')->paginate(15);
        return view('admin.edit-history.index', compact('logs'));
    }

    public function bulkAction(Request $request)
{
    $selected = $request->input('selected_logs', []);
    $action = $request->input('action');

    if (empty($selected)) {
        return redirect()->back()->with('status', 'Nevybrali ste žiadne záznamy.');
    }

    if ($action === 'delete') {
        EditHistory::whereIn('id', $selected)->delete();
        return redirect()->route('edit.history')->with('status', 'Záznamy boli vymazané.');
    }

    if ($action === 'export') {
        $logs = EditHistory::with(['user', 'pdfEdit'])->whereIn('id', $selected)->get();

        $csvData = "Používateľ;Čas;Akcia;Cez\n";
        foreach ($logs as $log) {
            $csvData .= "{$log->user->name};{$log->used_at};{$log->pdfEdit->name};{$log->accessed_via}\n";
        }

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vybrane_zaznamy.csv"',
        ]);
    }

    return redirect()->back()->with('status', 'Neznáma akcia.');
}

}
