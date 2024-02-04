<?php $this->extend('../BaseLayout/Layout.php'); ?>

<?php $this->section('main'); ?>

<div class="flex y_center x_center" style="min-height: 100vh;">
    <div class="flex_child mb_hide tb_hide overlay" style="height: 100vh; background: rgb(245, 245, 245);">
        <!-- <img src="https://kwkpasarminggu.or.id/assets/image/cover-1.png" alt="" style="width: 100%; height: 100%; object-fit: cover;"> -->
        <img src="https://img.freepik.com/free-photo/crop-hands-stacked-together-circle_23-2147846680.jpg" alt="" style="width: 100%; height: 100%; object-fit: cover; filter: blur(7px);">

        <div class="layer" style="width: 100%; height: 100%; content: ''; background: var(--colorBlack); opacity: 0.5;"></div>
    </div>
    <div class="flex_child flex y_center x_center p5 mb_p2" style="height: 100%; box-sizing: border-box;">
        <div class="flex_child" style="max-width: 450px;">
            <div class="tx_w_bolder tx_bg1 mb_tx_al_ct">
                Login akun anggota
            </div>

            <form method="post" class="form_target block mt3">
                <div class="tx_field1">
                    <div class="input_label">
                        <label for="username">
                            Username/email
                        </label>
                    </div>

                    <div class="input_item p0c5 mb_p0">
                        <input id="username" type="email" name="username" placeholder="Username atau email">
                    </div>

                    <div class="notif_text"></div>
                </div>

                <div class="tx_field1 mt2">
                    <div class="input_label">
                        <label for="email">
                            Password
                        </label>
                    </div>

                    <div class="input_item p0c5 mb_p0">
                        <input id="password" type="password" name="password" placeholder="Masukan password">
                    </div>

                    <div class="notif_text"></div>
                </div>

                <div class="flex x_end mt2">
                    <!-- Lupa password?
                    <a href="<?= member_url('reset_password'); ?>" class="orig_udline ml0c5">
                        Reset password
                    </a> -->
                </div>

                <button class="login button1 mt2 wd100pc p1c5" style="font-size: 1.15rem;">
                    Login
                </button>

                <div class="tx_al_ct mt2">
                    Belum punya akun?
                    <a href="<?= member_url('register'); ?>" class="orig_udline ml0c5 mb_block">
                        Daftar sekarang
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    // 
    $('body')
        .on('submit', '.form_target', function(e) {
            e.preventDefault();

            $(this).find('button.login').click();
        })
        .on('click', '.form_target button.login', function() {

            let button = this;
            let formTarget = $(this).parents('.form_target')[0];

            $(formTarget)
                .find('*[class*="tx_field"], *[class*="qty_field"]')
                .attr('ptx_validation', '')

            $.formCollect
                .target(formTarget)
                .required([{
                    name: 'username'
                }, {
                    name: 'password'
                }])
                .collect(
                    (json) => {

                        let url = $.makeURL.api().addPath('auth/account').href;

                        $(button).buttonOnLoading(true);

                        // Start send 
                        $.ajax({
                            type: 'POST',
                            url: url,
                            headers: {
                                'Authorization': 'Basic ' + btoa(json['username'] + ':' + json['password']),
                                'Content-type': 'application/json'
                            },
                            success: function(res) {

                                if (res.code == 200) {

                                    jsCookie.save({
                                        name: '_PTS-Auth:Token',
                                        value: res.data['token']
                                    });

                                    $.notif().success("Berhasil login ke sistem anggota koperasi");

                                    setTimeout(() => {

                                        location.reload();
                                    }, 150);
                                }

                                $(button).buttonOnLoading(false);
                            },
                            error: function({
                                responseJSON
                            }) {

                                if (typeof responseJSON === 'undefined') {
                                    $(button).buttonOnLoading(false);
                                    return;
                                }

                                switch (responseJSON.report_id) {
                                    case 'ALA1':
                                        $.notif().warning("Username, email, atau password yang anda masukan salah");
                                        break;
                                    default:
                                        $.notif().error('Kode error (' + responseJSON.report_id + ')');
                                        break;
                                }

                                $(button).buttonOnLoading(false);
                            }
                        });
                    },
                    (err) => {
                        console.log(err);

                        if ($.inArray(err.code, [undefined, null, '']) < 0 &&
                            err.code == 'REQUIRED_FORM_IS_EMPTY') {

                            let errDomParent = $(err.form.dom).parents('*[class*="tx_field"], *[class*="qty_field"]');

                            $(errDomParent).attr('ptx_validation', 'invalid')
                                .find('input, textarea').focus()
                                .end()
                                .find('.notif_text').remove()
                                .end();
                        }
                    }
                )
        });
</script>

<?php $this->endSection('main'); ?>