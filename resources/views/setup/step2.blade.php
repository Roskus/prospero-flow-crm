<header>
    <h1>{{ trans('hammer.Setup') }} - {{ trans('hammer.Step') }} 2</h1>
</header>
<form method="post" action="/setup/step3">  
    <div class="form-group">
        <label>Host</label>
        <input type="text" name="db_host" value="127.0.0.1" class="form-control" required="required">
    </div>
    
    <div class="form-group">
        <label>Port</label>
        <input type="text" name="db_port" value="3306" class="form-control" required="required">
    </div>
    
    <div class="form-group">
        <label>Database</label>
        <input type="text" name="db_database" value="" class="form-control" required="required">
    </div>
    
    <div class="form-group">
        <label>Username</label>
        <input type="text" name="db_username" value="" class="form-control" required="required">
    </div>
    
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="db_password" value="" class="form-control" required="required">
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">{{ trans('hammer.Next') }}</button>
    </div>
</form>