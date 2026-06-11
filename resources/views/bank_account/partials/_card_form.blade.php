<div class="card mt-4">
    <div class="card-header">
        <h6 class="mb-0">{{ __('Add card') }}</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('/bank-card/save') }}">
            @csrf
            <input type="hidden" name="bank_account_id" value="{{ $bank_account->id }}">
            <input type="hidden" name="id" id="card_edit_id" value="">

            <div class="row">
                <div class="col-12 col-md-3">
                    <label for="card_type">{{ __('Type') }} <span class="text-danger">*</span></label>
                    <select name="type" id="card_type" required class="form-select">
                        <option value="debit">{{ __('Debit') }}</option>
                        <option value="credit">{{ __('Credit') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="card_network">{{ __('Network') }} <span class="text-danger">*</span></label>
                    <select name="network" id="card_network" required class="form-select">
                        @foreach(\App\Models\BankCard::NETWORKS as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label for="card_cardholder_name">{{ __('Cardholder name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="cardholder_name" id="card_cardholder_name"
                           maxlength="80" required class="form-control text-uppercase"
                           placeholder="JOHN DOE">
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-12 col-md-4">
                    <label for="card_number">{{ __('Card number') }} <span class="text-danger" id="card_number_required">*</span></label>
                    <input type="text" name="number" id="card_number"
                           maxlength="19" class="form-control font-monospace"
                           placeholder="**** **** **** ****"
                           inputmode="numeric">
                    <div class="text-muted small mt-1" id="card_number_hint"></div>
                </div>
                <div class="col-12 col-md-2">
                    <label for="card_cvv">CVV</label>
                    <input type="password" name="cvv" id="card_cvv"
                           maxlength="4" class="form-control font-monospace"
                           placeholder="***"
                           inputmode="numeric">
                </div>
                <div class="col-12 col-md-2">
                    <label for="card_expires_month">{{ __('Exp. month') }} <span class="text-danger">*</span></label>
                    <select name="expires_month" id="card_expires_month" required class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="card_expires_year">{{ __('Exp. year') }} <span class="text-danger">*</span></label>
                    <select name="expires_year" id="card_expires_year" required class="form-select">
                        @for($y = date('Y'); $y <= date('Y') + 15; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
