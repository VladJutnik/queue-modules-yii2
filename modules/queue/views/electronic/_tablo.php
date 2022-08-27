<style>
    #refreshButton {
        visibility: hidden;
    }

    nav {
        visibility: hidden;
    }
</style>
<div class="row_electronic">
    <table class="col-8 table_electronic">
        <thead>
        <tr class="text-center">
            <th class="text-center" colspan="2"><span class="text_el_yellow">Движение по этажам </span><span
                        class="text_el_yellow">(пройдите на этаж, указанный напротив Вашей
                    фамилии)</span>
            </th>
        </tr>
        <tr>
            <th class="text_el_yellow">Направление</th>
            <th class="text_el_white text_el_font_25">Пациенты</th>
        </tr>
        </thead>
        <tbody>
        <?
        $array_floor = [];
        foreach ($listPatientFloorAll as $key => $one) {
            $array_floor[$one['active_floor']][] = $one['patient']['card_number']; ?>
            <?
        } ?>
        <tr class="">
            <td class="table_el_background text_el_yellow"><strong><img width="50" height="50"
                                                                        src="../../images/chel_color.png" alt=""/> на
                    этаж 1</td>
            <td class="table_el_background text_el_white text_el_font_30"><?= ($array_floor[1]) ? implode(', ', $array_floor[1]) : '-' ?></td>
        </tr>
        <tr class="">
            <td class="table_el_background text_el_yellow"><strong><img width="50" height="50"
                                                                        src="../../images/chel_color.png" alt=""/> на
                    этаж 2</td>
            <td class="table_el_background text_el_white text_el_font_30"><?= ($array_floor[2]) ? implode(', ', $array_floor[2]) : '-' ?></td>
        </tr>
        <tr class="">
            <td class="table_el_background text_el_yellow"><strong><img width="50" height="50"
                                                                        src="../../images/chel_color.png" alt=""/> на
                    этаж 3</td>
            <td class="table_el_background text_el_white text_el_font_30"><?= ($array_floor[3]) ? implode(', ', $array_floor[3]) : '-' ?></td>
        </tr>
        </tbody>
    </table>
    <table class="col-4 table_electronic">
        <thead>
        <tr class="text-center">
            <th class="text-center text_el_yellow" colspan="2">Приглашение в кабинет (пройдите в кабинет)</th>
        </tr>
        <tr>
            <th class="text_el_white text_el_font_25">Пациент</th>
            <th class="text_el_yellow">Кабинет</th>
        </tr>
        </thead>
        <tbody>
        <?
        foreach ($listPatientFloor as $one) {
            if ($one['active_cabinet']) {
                ?>
                <tr class="text_el_border">
                    <td class="table_el_background text_el_white text_el_font_30">
                        <strong><?= $one['patient']['card_number'] ?> </strong></td>
                    <td class="table_el_background text_el_font_50 text_el_yellow"><img width="80" height="80"
                                                                                        src="../../images/chel3.png"
                                                                                        alt=""/><?= $one['active_cabinet'] ?>
                    </td>
                </tr>
                <?
            }
        } ?>
        </tbody>
    </table>
</div>