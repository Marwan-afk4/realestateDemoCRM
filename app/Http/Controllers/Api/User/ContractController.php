<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Mail\ContractAgreedMail;
use App\Models\Contract;
use App\Models\ContractAgreement;
use App\Services\ContractPdfGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::all();
        return response()->json(['contracts' => $contracts]);
    }

    public function show($id)
    {
        $contract = Contract::find($id);
        if (!$contract) {
            return response()->json(['message' => 'Contract not found'], 404);
        }
        return response()->json(['contract' => $contract]);
    }

    public function agree(Request $request, $id)
    {
        $contract = Contract::find($id);
        if (!$contract) {
            return response()->json(['message' => 'Contract not found'], 404);
        }

        $user = $request->user();

        // Check if already agreed
        $existingAgreement = ContractAgreement::where('contract_id', $contract->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingAgreement) {
            return response()->json(['message' => 'You have already agreed to this contract'], 400);
        }

        $agreement = ContractAgreement::create([
            'contract_id' => $contract->id,
            'user_id' => $user->id,
        ]);

        $this->sendAgreementEmail($user, $contract, $agreement);

        return response()->json(['message' => 'Successfully agreed to the contract']);
    }


    public function agreedContracts(Request $request)
    {
        $user = $request->user();
        $agreedContracts = ContractAgreement::where('user_id', $user->id)
        ->with(['contract'])
        ->get();
        return response()->json(['agreedContracts' => $agreedContracts]);
    }

    public function downloadPdf(Request $request, $id, ContractPdfGenerator $pdfs)
    {
        $contract = Contract::find($id);
        if (!$contract) {
            return response()->json(['message' => 'Contract not found'], 404);
        }

        $user = $request->user();
        $agreement = ContractAgreement::where('contract_id', $contract->id)
            ->where('user_id', $user->id)
            ->first();

        $pdf = $pdfs->generate($user, $contract, $agreement);

        return response($pdf['contents'], 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$pdf['filename'].'"',
        ]);
    }

    protected function sendAgreementEmail($user, Contract $contract, ContractAgreement $agreement): void
    {
        if (! filled($user->email)) {
            return;
        }

        try {
            Mail::to($user->email)
                ->locale(app()->getLocale())
                ->send(new ContractAgreedMail($user, $contract, $agreement));
        } catch (\Throwable $e) {
            Log::error('Failed to send contract agreement email.', [
                'user_id' => $user->id,
                'contract_id' => $contract->id,
                'agreement_id' => $agreement->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
