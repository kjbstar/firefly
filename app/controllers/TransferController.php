<?php

/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/AccountHelper.php');
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/TransferHelper.php');

/**
 * Class TransferController
 */
class TransferController extends BaseController
{

    /**
     * Add a new transfer
     *
     * @return View
     */
    public function showIndex()
    {
        $transfers = Auth::user()->transfers()->with(['accountto', 'accountfrom'])->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')->paginate(50);

        return View::make('transfers.index')->with('title', 'All transfers')
            ->with('transfers', $transfers);
    }

    /**
     * Add a transfer (to an account)
     *
     * @return View
     */
    public function add()
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
            $prefilled = TransferHelper::emptyPrefilledAray();
        } else {
            $prefilled = TransferHelper::prefilledFromOldInput();

        }
        $accounts = AccountHelper::accountsAsSelectList();
        return View::make('transfers.add')->with(
            'title', 'Add a transfer'
        )->with('accounts', $accounts)->with('prefilled', $prefilled);
    }

    /**
     * Post process a new transfer.
     *
     * @return Redirect
     */
    public function postAdd()
    {
        $accountFrom = Auth::user()->accounts()->find(intval(Input::get('accountfrom_id')));
        $accountTo = Auth::user()->accounts()->find(intval(Input::get('accountto_id')));
        if (is_null($accountFrom)) {
            Session::flash('error', 'Invalid account (from) selected.');
            return Redirect::route('addtransfer')->withInput();
        }
        if (is_null($accountTo)) {
            Session::flash('error', 'Invalid account (to) selected.');
            return Redirect::route('addtransfer')->withInput();
        }

        $data = [
            'description'     => Input::get('description'),
            'amount'          => floatval(Input::get('amount')),
            'date'            => Input::get('date'),
            'ignoreallowance' => intval(Input::get('ignoreallowance'))
        ];

        $transfer = new Transfer($data);
        $transfer->accountto()->associate($accountTo);
        $transfer->accountfrom()->associate($accountFrom);


        /** @noinspection PhpParamsInspection */
        $transfer->user()->associate(Auth::user());


        $validator = Validator::make($transfer->toArray(), Transfer::$rules);
        if ($validator->fails()) {
            Session::flash('error', 'Could not save transfer.');
            return Redirect::route('addtransfer')->withInput()->withErrors($validator);
        }

        $result = $transfer->save();
        // @codeCoverageIgnoreStart
        if (!$result) {
            Session::flash('error', 'Could not save transfer.');
            return Redirect::route('addtransfer')->withInput()->withErrors($validator);
        }
        // @codeCoverageIgnoreEnd

        // now we can finally add the components:
        // save all components (if any):
        $transfer->saveComponentsFromInput();

        Session::flash('success', 'The transfer has been created.');

        return Redirect::to(Session::get('previous'));
    }

    /**
     * Show the view to edit a transfer
     *
     * @param Transfer $transfer The transfer
     *
     * @return \Illuminate\View\View
     */
    public function edit(Transfer $transfer)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
            $prefilled = TransferHelper::prefilledFromTransfer($transfer);
        } else {
            $prefilled = TransferHelper::prefilledFromOldInput();
        }
        $accounts = AccountHelper::accountsAsSelectList();

        return View::make('transfers.edit')->with('transfer', $transfer)->with(
            'accounts', $accounts
        )->with('title', 'Edit transfer ' . $transfer->description)->with('prefilled', $prefilled);
    }

    /**
     * Process the changes to the transfer.
     *
     * @param Transfer $transfer the transfer.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function postEdit(Transfer $transfer)
    {
        // update the transaction:
        $accountFrom = Auth::user()->accounts()->find(intval(Input::get('accountfrom_id')));
        $accountTo = Auth::user()->accounts()->find(intval(Input::get('accountto_id')));
        if (is_null($accountFrom)) {
            Session::flash('error', 'Invalid account (from) selected.');
            return Redirect::route('addtransfer')->withInput();
        }
        if (is_null($accountTo)) {
            Session::flash('error', 'Invalid account (to) selected.');
            return Redirect::route('addtransfer')->withInput();
        }

        $transfer->description = Input::get('description');
        $transfer->amount = floatval(Input::get('amount'));
        $transfer->date = Input::get('date');
        $transfer->accountto()->associate($accountTo);
        $transfer->accountfrom()->associate($accountFrom);
        $transfer->ignoreallowance = is_null(Input::get('ignoreallowance')) ? 0 : 1;

        // validate and save:
        $validator = Validator::make(
            $transfer->toArray(), Transfer::$rules
        );
        if ($validator->fails()) {
            Session::flash('error', 'The transfer could not be saved.');
            Log::debug('These rules failed: ' . print_r($validator->messages()->all(), true));
            return Redirect::route('edittransfer', $transfer->id)
                ->withInput()->withErrors($validator);
        } else {
            // try another save.
            $result = $transfer->save();
            // @codeCoverageIgnoreStart
            if (!$result) {
                Session::flash('error', 'The transfer could not be saved.');
                Log::debug('These rules failed: ' . print_r($validator->messages()->all(), true));
                return Redirect::route('edittransfer', $transfer->id)
                    ->withInput()->withErrors($validator);
            }
            // @codeCoverageIgnoreEnd

            // now add or update the components from the input:
            $transfer->saveComponentsFromInput();
            Cache::userFlush();
            Session::flash('success', 'The transfer has been saved.');

            return Redirect::to(Session::get('previous'));
        }
    }

    /**
     * Delete a transfer
     *
     * @param Transfer $transfer The transfer
     *
     * @return \Illuminate\View\View
     */
    public function delete(Transfer $transfer)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }

        return View::make('transfers.delete')->with('transfer', $transfer)
            ->with('title', 'Delete transfer ' . $transfer->description);
    }

    /**
     * Actually delete the transfer (POST).
     *
     * @param Transfer $transfer The transfer
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDelete(Transfer $transfer)
    {
        $transfer->delete();
        Session::flash('success', 'The transfer has been deleted.');

        return Redirect::to(Session::get('previous'));
    }
}
