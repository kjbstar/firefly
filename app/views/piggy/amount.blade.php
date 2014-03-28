<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Add or remove money
                from "{{{$pig->name}}}"</h4>
        </div>
        {{Form::open(['class' => 'form-inline','role' => 'form'])}}
        <div class="modal-body">
            <p>
                Add to, or remove money from this piggy bank. The current
                amount in this piggy bank is {{mf
                ($pig->amount,true)}}.
                @if($balance > 0)
                You can <em>add</em> up to {{mf($balance)}} to this piggy bank.
                @else
                No money is left to be put in this piggy bank.
                @endif
            </p>
            @if($balance <= 0)
            <p>
                If you need to add more, remove it from other piggy banks
                first, or save more money.
            </p>
            @endif
            <p>
               Use a negative amount to remove money, and a positive amount
                to add to it.
            </p>
            <div class="form-group">
                <label class="col-sm-3
            control-label" for="inputAmount">Amount</label>

                <div class="col-sm-9">
                    <div class="input-group">
                        <span class="input-group-addon">&euro;</span>
                        @if($balance < $pig->amount*-1)
                        <input type="number" max="-{{$pig->amount}}"
                               min="{{$balance}}" value="0"
                               step="any"
                               name="amount"
                               class="form-control" id="inputAmount">
                        @else
                        <input type="number" max="{{$balance}}"
                               min="-{{$pig->amount}}" value="0"
                               step="any"
                               name="amount"
                               class="form-control" id="inputAmount">
                        @endif
                    </div>
                </div>
            </div>


        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Add / remove</button>
        </div>
        {{Form::close()}}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->