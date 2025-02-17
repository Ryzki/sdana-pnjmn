    <section class="wrapper">
        <div class="row">
            <div class="col-lg-7">
                <section class="panel">
                    <header class="panel-heading">
                        <?php echo ($add_mode==1)? 'ADD' : 'EDIT'; ?> USER
                    </header>
                    <div class="panel-body">
                        <form class="form-horizontal form-validation" id="formID" method="POST" >
                            <input type="hidden" name="uid" class="form-control" value="<?php echo (isset($EDIT['id_system_user'])? $EDIT['id_system_user'] : ''); ?>">

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Username <span class="text-danger">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" name="username" class="form-control" value="<?php echo (isset($EDIT['username'])? $EDIT['username'] : ''); ?>" data-validation-engine="validate[required]" data-errormessage-value-missing="USERNAME is required!">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Password <span class="text-danger">*</span></label>
                                <div class="col-sm-6">
                                    <input type="password" name="password" id="password" class="form-control" data-validation-engine="validate[required,minSize[6]]" data-errormessage-value-missing="PASSWORD is required!">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Re-type Password <span class="text-danger">*</span></label>
                                <div class="col-sm-6">
                                    <input type="password" name="password2" class="form-control" data-validation-engine="validate[required,equals[password]]" data-errormessage-value-missing="Re-type PASSWORD here">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Full Name <span class="text-danger">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" name="fullname" class="form-control" value="<?php echo (isset($EDIT['fullname'])? $EDIT['fullname'] : ''); ?>" data-validation-engine="validate[required]" data-errormessage-value-missing="FULLNAME is required!">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">E-Mail</label>
                                <div class="col-sm-6">
                                    <input type="text" name="email" class="form-control" value="<?php echo (isset($EDIT['email'])? $EDIT['email'] : ''); ?>" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Phone</label>
                                <div class="col-sm-6">
                                    <input type="text" name="telpon" class="form-control" value="<?php echo (isset($EDIT['phone'])? $EDIT['phone'] : ''); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Privilege <span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                  <select name="privilege" class="form-control" id="privilege" style="width:150px;">
                                    <?php 
                                    foreach ($usergroup as $key) {
                                        $selected = '';
                                        if ($key['id_group'] == $EDIT['id_group'])
                                        {
                                            $selected = 'selected="selected"';
                                        }
                                    ?>
                                    <option value="<?php echo $key['id_group']; ?>" <?php echo $selected; ?> ><?php echo $key['group_name']; ?></option>
                                    <?php } ?>
                                  </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Status</label>
                                <div class="col-md-6">
                                  <select name="status" class="form-control" id="userform" style="width:150px;">
                                    <option value="1" <?php echo (($add_mode==1 OR $EDIT['active']==1)? 'selected="selected"' : '') ?> >Active &nbsp; &nbsp;</option>
                                    <option value="0" <?php echo (($EDIT['active']==0)? 'selected="selected"' : '') ?> >Not Active</option>
                                  </select>
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