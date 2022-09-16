<div class="account-detail-box">
    <h3 class="account-heading">{{$labels['change_password']}}</h3>
    <form action="#" name="change_password_form" id="change_password_form" method="post" enctype="multipart/form-data">
        <div class="input-outer ">
            <div class="hide-show-password">
                <input type="password" name="old_password" id="old_password" placeholder="{{$labels['current_password']}}" data-error="#old_password_error">
                <div class="toggle-password">
                    <span class="show-password">{{$labels['show']}}</span>
                    <span class="hide-password">{{$labels['hide']}}</span>
                </div>
            </div>
            <span class="error-login" id="old_password_error"></span>
        </div>
        <div class="input-outer ">
            <div class="hide-show-password">
                <input type="password" name="new_password" id="new_password" data-error="#new_password_error" placeholder="{{$labels['new_password']}}">
                <div class="toggle-password">
                    <span class="show-password">{{$labels['show']}}</span>
                    <span class="hide-password">{{$labels['hide']}}</span>
                </div>
            </div>
            <span class="error-login" id="new_password_error"></span>
        </div>
        <div class="input-outer ">
            <div class="hide-show-password">
                <input type="password" name="confirm_password" id="confirm_password" data-error="#confirm_password_error" placeholder="{{$labels['confirm_password']}}">
                <div class="toggle-password">
                    <span class="show-password">{{$labels['show']}}</span>
                    <span class="hide-password">{{$labels['hide']}}</span>
                </div>
            </div>
            <span class="error-login" id="confirm_password_error"></span>
        </div>
        <button class="comman-btn" type="submit" id="change_password_submit">{{$labels['save']}}</button>
    </form>
</div>