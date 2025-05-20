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
        $logs = EditHistory::with(['user', 'pdfEdit'])->orderByDesc('used_at')->get();
        return view('admin.edit-history.index', compact('logs'));
    }

    public function bulkAction(Request $request)
{
    $selected = $request->input('selected', []);
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

        $csvData = __('history.page.usage.table.user') . ";" . 
                __('history.page.usage.table.time') . ";" .
                __('history.page.usage.table.used') . ";" .
                __('history.page.usage.table.type') . "\n";
        foreach ($logs as $log) {
            $csvData .= "{$log->user->name};{$log->used_at};{$log->pdfEdit->name};{$log->accessed_via}\n";
        }

        $filename = __('history.page.usage.export.filename', ['date' => now()->format('Y-m-d')]);

        return Response::make("\xEF\xBB\xBF" . $csvData, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'. $filename . '"',
        ]);
    }

    return redirect()->back()->with('status', 'Neznáma akcia.');
}

}
