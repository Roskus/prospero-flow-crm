<header>
    <h1>{{ trans('Setup') }} - {{ trans('Step') }} 1</h1>
</header>
<form method="post" action="/setup/step2">
    <div class="form-group">
        <label>{{ trans('hammer.Choose language') }}:</label>
        <select name="language" id="language" required="required">
            <option value="en" selected="selected">English</option>
            <option value="es" selected="selected">Spanish</option>
        </select>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary">{{ trans('Next') }}</button>
    </div>
</form>
