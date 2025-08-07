<?php $this->extend('Layout/Layout.php'); ?>

<!-- Sections -->

<?php $this->section('content'); ?>

<div class="block">

    <div class="content_box">
        <div class="initial mb1c5">
            <div class="title">
                Form anggota
            </div>
        </div>

        <div class="form_target box_body mt1">

            <input type="hidden" name="member_id" value="<?= base64_encode($memberData['member_id']); ?>">

            <!-- Data member - start -->
            <div class="flex y_start x_start mb_block tb_block pt2" style="border-top: 1px solid rgb(200, 200, 200);">
                <div class="flex_child">
                    <div class="tx_field1 wd100pc">
                        <div class="input_label">
                            <label for="register_number">
                                Nomor anggota <span style="color: red">*</span>
                            </label>
                        </div>

                        <div class="input_item">
                            <input type="text" name="register_number" id="register_number" placeholder="123456789" value="<?= $memberData['register_number']; ?>">
                        </div>
                    </div>

                    <div class="tx_field1 mt2 ">
                        <div class="input_label">
                            <label for="nickname">
                                Nama panggilan
                            </label>
                        </div>

                        <div class="input_item">
                            <input type="text" name="nickname" id="nickname" placeholder="Cth: John" value="<?= $memberData['nickname']; ?>">
                        </div>
                    </div>

                    <div class="tx_field1 mt2">
                        <div class="input_label">
                            <label for="address_domicile">
                                Alamat domisili <span style="color: red">*</span>
                            </label>
                        </div>

                        <div class="input_item">
                            <textarea name="address_domicile" id="address_domicile" placeholder="Jl. Raya No. 12 Rt. 11/Rw. 02"><?= $memberData['address_domicile']; ?></textarea>
                        </div>
                    </div>

                    <div class="tx_field1 mt2 wd100pc">
                        <div class="input_label">
                            <label for="phone_number">
                                Nomor telepon <span style="color: red">*</span>
                            </label>
                        </div>

                        <div class="input_item">
                            <div class="addon inline tx_w_bold">
                                62
                            </div>
                            <input type="text" name="phone_number" id="phone_number" placeholder="81234567899" value="<?= printPhoneNumber('62', '', $memberData['phone_number']); ?>">
                        </div>
                    </div>

                    <div class="tx_field1 mt2 wd100pc">
                        <div class="input_label">
                            <label for="wa_number">
                                Nomor whatsapp
                            </label>
                        </div>

                        <div class="input_item">
                            <div class="addon inline tx_w_bold">
                                62
                            </div>
                            <input type="text" name="wa_number" id="wa_number" placeholder="81234567899" value="<?= printPhoneNumber('62', '', $memberData['wa_number']); ?>">
                        </div>
                    </div>
                </div>
                <div class="flex_child tx_w_bolder mb_hide tb_hide ml3">
                    Data calon anggota
                </div>
            </div>
            <!-- Data member - end -->

            <!-- Data member identity - start -->
            <div class="flex y_start x_start mb_block tb_block mt2 pt2" style="border-top: 1px solid rgb(200, 200, 200);">
                <div class="flex_child">
                    <div class="tx_field1 wd100pc">
                        <div class="input_label">
                            <label for="nik">
                                NIK (sesuai KTP) <span style="color: red">*</span>
                            </label>
                        </div>

                        <div class="input_item">
                            <input type="text" name="nik" id="nik" placeholder="1234567890" value="<?= $memberData['identity']['nik']; ?>">
                        </div>
                    </div>

                    <div class="tx_field1 mt2 wd100pc">
                        <div class="input_label">
                            <label for="fullname">
                                Nama lengkap (sesuai KTP) <span style="color: red">*</span>
                            </label>
                        </div>

                        <div class="input_item">
                            <input type="text" name="fullname" id="fullname" placeholder="Cth: John Doe" value="<?= $memberData['identity']['fullname']; ?>">
                        </div>
                    </div>

                    <div class="flex flex_gap1 y_start x_start mt2">
                        <div class="tx_field1 wd100pc">
                            <div class="input_label">
                                <label for="birth_place">
                                    Tempat lahir <span style="color: red">*</span>
                                </label>
                            </div>

                            <div class="input_item">
                                <input type="text" name="birth_place" id="birth_place" placeholder="Cth: Jakarta" value="<?= $memberData['identity']['birth_place']; ?>">
                            </div>
                        </div>

                        <div class="tx_field1 wd100pc">
                            <div class="input_label">
                                <label for="birth_date">
                                    Tanggal lahir <span style="color: red">*</span>
                                </label>
                            </div>

                            <div class="input_item">
                                <input type="date" name="birth_date" id="birth_date" placeholder="" value="<?= $memberData['identity']['birth_date']; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="tx_field1 mt2">
                        <div class="input_label">
                            <label>
                                Jenis kelamin <span style="color: red">*</span>
                            </label>
                        </div>

                        <div class="flex flex_gap1 y_start x_start">

                            <div class="flex y_center x_start">
                                <label class="radio1" for="male" type="radio">
                                    <input id="male" type="radio" name="gender" value="M" <?= $memberData['identity']['gender'] == 'M' ? 'checked' : ''; ?>>
                                </label>

                                <div class="flex_child ml0c5">
                                    Laki - laki
                                </div>
                            </div>

                            <div class="flex y_center x_start">
                                <label class="radio1" for="female" type="radio">
                                    <input id="female" type="radio" name="gender" value="F" <?= $memberData['identity']['gender'] == 'F' ? 'checked' : ''; ?>>
                                </label>

                                <div class="flex_child ml0c5">
                                    Perempuan
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tx_field1 mt2">
                        <div class="input_label">
                            <label for="address">
                                Alamat (sesuai KTP) <span style="color: red">*</span>
                            </label>
                        </div>

                        <div class="input_item">
                            <textarea name="address" id="address" placeholder="Jl. Raya No. 12 Rt. 11/Rw. 02"><?= $memberData['identity']['address']; ?></textarea>
                        </div>
                    </div>

                    <div class="tx_field1 mt2 wd100pc">
                        <div class="input_label">
                            <label for="npwp">
                                NPWP Pribadi
                            </label>
                        </div>

                        <div class="input_item">
                            <input type="text" name="npwp" id="npwp" placeholder="00.00.00.0-000.000" value="<?= $memberData['identity']['npwp']; ?>">
                        </div>
                    </div>
                </div>
                <div class="flex_child tx_w_bolder mb_hide tb_hide ml3">
                    Data KTP
                </div>
            </div>
            <!-- Data member identity = end -->

            <!-- Data member business - start -->
            <div class="flex y_start x_start mb_block tb_block mt3 pt1" style="border-top: 1px solid rgb(200, 200, 200);">
                <div class="flex_child">

                    <input type="hidden" name="registration_type" value="NIB">

                    <!-- <div class="tx_field1 mt2 wd50pc">
                    <div class="input_label">
                        <label for="registration_type">
                            Jenis izin usaha <span style="color: red">*</span>
                        </label>
                    </div>

                    <div class="input_item">
                        <select name="registration_type">
                            <option value="" selected disabled>-- Pilih izin usaha --</option>
                            <option value="SIUP">Surat Izin Usaha Perdagangan (SIUP)</option>
                            <option value="NIB">NIB (Nomor Induk Berusaha)</option>
                            <option value="CV"></option>
                        </select>
                    </div>
                </div> -->

                    <div class="tx_field1 mt2">
                        <div class="input_label">
                            <label>
                                Izin usaha
                            </label>
                        </div>

                        <?php {
                            $businessLegals = [
                                ['NIB', 'NIB (Nomor Induk Berusaha)'],
                                ['NPWP', 'NPWP Usaha'],
                                ['PIRT', 'PIRT (Pangan Industri Rumah Tangga)'],
                                ['BPOM', 'BPOM'],
                                ['HALAL', 'Sertifikasi Halal'],
                                ['Depkes', 'Depkes'],
                                ['HAKI', 'HaKI (Hak Kekayaan Intelektual)'],
                            ];
                        } ?>

                        <?php foreach ($businessLegals as $value) : ?>

                            <div class="flex y_center x_start mt1">
                                <label class="checkbox1" for="business_legal:<?= $value[0]; ?>">
                                    <input id="business_legal:<?= $value[0]; ?>" type="checkbox" name="business_legal" value="<?= $value[0]; ?>" <?= is_array($memberData['business']['business_legal']) && in_array($value[0], $memberData['business']['business_legal']) ? 'checked' : ''; ?>>
                                </label>

                                <div class="flex_child ml0c5">
                                    <?= $value[1]; ?>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>

                    <div class="tx_field1 mt2 wd50pc">
                        <div class="input_label">
                            <label for="registration_number">
                                Nomor Induk Berusaha
                            </label>
                        </div>

                        <div class="input_item">
                            <input type="text" name="registration_number" id="registration_number" placeholder="1234567890000" value="<?= $memberData['business']['registration_number']; ?>">
                        </div>
                    </div>

                    <div class="tx_field1 mt2 wd50pc">
                        <div class="input_label">
                            <label for="business_registration_date">
                                Tanggal pengesahan NIB
                            </label>
                        </div>

                        <div class="input_item">
                            <input type="date" name="business_registration_date" id="business_registration_date" placeholder="" value="<?= $memberData['business']['registration_date']; ?>">
                        </div>
                    </div>

                    <div class="tx_field1 mt2 wd50pc">
                        <div class="input_label">
                            <label for="business_name">
                                Nama usaha
                            </label>
                        </div>

                        <div class="input_item">
                            <input type="text" name="business_name" id="business_name" placeholder="Cth: PT. Kreasi Putra Subing" value="<?= $memberData['business']['business_name']; ?>">
                        </div>
                    </div>

                    <div class="tx_field1 mt2">
                        <div class="input_label">
                            <label for="business_address">
                                Alamat usaha
                            </label>
                        </div>

                        <div class="input_item">
                            <textarea name="business_address" id="business_address" placeholder="Jl. Raya No. 12 Rt. 11/Rw. 02"><?= $memberData['business']['business_address']; ?></textarea>
                        </div>
                    </div>

                    <div class="tx_field1 mt2 wd100pc">
                        <div class="input_label">
                            <label for="business_npwp">
                                NPWP usaha
                            </label>
                        </div>

                        <div class="input_item">
                            <input type="text" name="business_npwp" id="business_npwp" placeholder="00.00.00.0-000.000" value="<?= $memberData['business']['business_npwp']; ?>">
                        </div>
                    </div>

                    <div class="tx_field1 mt2 wd100pc">
                        <div class="input_label">
                            <label for="business_phone_number">
                                Nomor telepon usaha
                            </label>
                        </div>

                        <div class="input_item">
                            <div class="addon inline tx_w_bold">
                                62
                            </div>
                            <input type="text" name="business_phone_number" id="business_phone_number" placeholder="81234567899" value="<?= printPhoneNumber('62', '', $memberData['business']['business_phone_number']); ?>">
                        </div>
                    </div>

                    <div class="tx_field1 mt2 wd100pc">
                        <div class="input_label">
                            <label for="business_email">
                                Email usaha
                            </label>
                        </div>

                        <div class="input_item">
                            <input type="text" name="business_email" id="business_email" placeholder="Cth: myemail@mail.com" value="<?= $memberData['business']['business_email']; ?>">
                        </div>
                    </div>
                </div>
                <div class="flex_child tx_w_bolder mb_hide tb_hide ml3">
                    Data Izin Usaha
                </div>
            </div>
            <!-- Data member business - end -->

            <div class="flex y_center x_start mt1 wd_fit">
                <i class="ri-information-line mr1"></i>
                <div class="flex_child">
                    <span style="color: red;">*</span>wajib diisi
                </div>
            </div>

            <button class="submit button1 mt2 wd100pc" style="max-width: 150px;">
                Kirim
            </button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('body')
        .on('click', '.form_target button.submit', function() {

            let formTarget = $(this).parents('.form_target')[0],
                button = this;

            $(formTarget)
                .find('*[class*="tx_field"], *[class*="qty_field"]')
                .attr('ptx_validation', '')

            $.formCollect
                .target(formTarget)
                .required([{
                    name: 'register_number'
                }, {
                    name: 'address_domicile'
                }, {
                    name: 'phone_number'
                }, {
                    name: 'nik'
                }, {
                    name: 'fullname'
                }, {
                    name: 'birth_place'
                }, {
                    name: 'birth_date'
                }, {
                    name: 'gender'
                }, {
                    name: 'address'
                }])
                .collect(
                    (json) => {

                        json['phone_number'] = "62" + json['phone_number'];
                        json['wa_number'] = "62" + json['wa_number'];

                        if (typeof json['business_phone_number'] != 'undefined')
                            json['business_phone_number'] = "62" + json['business_phone_number'];

                        let url = $.makeURL.api().addPath('manage/member/manual/' + json['member_id']).href;
                        let formData = jsonToFormData(json);

                        $(button).buttonOnLoading(true);

                        // Start send 
                        $.ajax({
                            type: 'PUT',
                            url: url,
                            headers: {
                                'Authorization': 'Bearer ' + jsCookie.get('_PTS-Auth:Token')
                            },
                            data: formData,
                            success: function(res) {
                                if (res.code == 200) {
                                    $.notif().success("Update data anggota berhasil");

                                    let redirectUrl = $.makeURL.member().addPath('manage/member/' + json['member_id']).href;
                                    window.location.href = redirectUrl;
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
                                    default:
                                        $.notif().error('Kode error (' + responseJSON.report_id + ')');
                                        break;
                                }

                                $(button).buttonOnLoading(false);
                            }
                        });
                    },
                    (err) => {
                        // console.log(err);

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
                );
        });
</script>


<?php $this->endSection('content'); ?>