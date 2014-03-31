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
        $transfers = Auth::user()->transfers()->orderBy('date', 'DESC')
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
        $data = ['description'    => Input::get('description'),
                 'amount'         => floatval(Input::get('amount')),
                 'accountfrom_id' => intval(Input::get('accountfrom_id')),
                 'accountto_id'   => intval(Input::get('accountto_id')),
                 'date'           => Input::get('date'),
                 'user_id'        => Auth::user()->id];
        $transfer = new Transfer($data);

        // explode every object at the / and see if there is one.
        // more than one? return to Transaction:
        $beneficiary = ComponentHelper::saveComponentFromText('beneficiary', Input::get('beneficiary'));
        $category = ComponentHelper::saveComponentFromText('category', Input::get('category'));
        $budget = ComponentHelper::saveComponentFromText('budget', Input::get('budget'));


        $validator = Validator::make($transfer->toArray(), Transfer::$rules);
        if ($validator->fails()) {
            Session::flash('error', 'Could not add transfer.');
            return Redirect::route('addtransfer')->withInput()->withErrors($validator);
        } else {
            $result = $transfer->save();
            if (!$result) {
                Session::flash('error', 'Could not add transfer.');
                return Redirect::route('addtransfer')->withInput()->withErrors($validator);
            }
            // attach the beneficiary, if it is set:
            $transfer->attachComponent($beneficiary);
            $transfer->attachComponent($budget);
            $transfer->attachComponent($category);

            Session::flash('success', 'The transfer has been created.');

            return Redirect::to(Session::get('previous'));
        }
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
        $fromAccount = Auth::user()->accounts()->find(
            intval(Input::get('accountfrom_id'))
        );
        $toAccount = Auth::user()->accounts()->find(
            intval(Input::get('accountto_id'))
        );

        $transfer->description = Input::get('description');
        $transfer->amount = floatval(Input::get('amount'));
        $transfer->date = Input::get('date');
        if ($fromAccount) {
            $transfer->accountfrom()->associate($fromAccount);
        }
        if ($toAccount) {
            $transfer->accountto()->associate($toAccount);
        }

        // explode every object at the / and see if there is one.
        // more than one? return to Transaction:
        $beneficiary = ComponentHelper::saveComponentFromText('beneficiary', Input::get('beneficiary'));
        $category = ComponentHelper::saveComponentFromText('category', Input::get('category'));
        $budget = ComponentHelper::saveComponentFromText('budget', Input::get('budget'));

        $validator = Validator::make(
            $transfer->toArray(), Transfer::$rules
        );
        if ($validator->fails()) {
            Session::flash('error', 'Could not edit transfer.');
            return Redirect::route('edittransfer', $transfer->id)->withInput()->withErrors($validator);
        } else {
            $result = $transfer->save();
            if (!$result) {
                Session::flash('error', 'Could not edit transfer.');
                return Redirect::route('edittransfer', $transfer->id)->withInput()->withErrors($validator);
            } else {


                // detach all components first:
                $transfer->components()->sync([]);
                // attach the beneficiary, if it is set:
                $transfer->attachComponent($beneficiary);
                $transfer->attachComponent($budget);
                $transfer->attachComponent($category);
            }

            Session::flash('success', 'The transfer has been edited.');

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
