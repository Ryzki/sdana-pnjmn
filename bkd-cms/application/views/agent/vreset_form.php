    <section class="wrapper">
        <div class="row">
            <div class="col-lg-7">
                <section class="panel">
                    <header class="panel-heading">
                        Reset Agent Password
                    </header>
                    <div class="panel-body">
                        <form class="form-horizontal form-validation" id="formID" method="POST" action="<?php echo site_url('agent/submit_reset_password'); ?>">
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Username</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" value="<?php echo $EDIT['agent_username']; ?>" disabled="disabled">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Password</label>
                                <div class="col-sm-6">
                                    <input type="password" name="password" id="password" class="form-control" data-validation-engine="validate[required,minSize[6]]" data-errormessage-value-missing="PASSWORD is required!">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Re-type Password</label>
                                <div class="col-sm-6">
                                    <input type="password" name="password2" class="form-control" data-validation-engine="validate[required,equals[password]]" data-errormessage-value-missing="Re-type PASSWORD here">
                                </div>
                            </div>
                             
                            <div class="position-center">
                                <button type="submit" class="btn btn-primary">Submit</button> 
                                <button type="button" class="btn btn-white" id="btn-cancel" onclick="history.back(-1)">Cancel</button>
                            </div>  
                        </form>
                    </div>
                </section>
            </div>            
        </div>
    </section>