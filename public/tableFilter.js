$(document).ready(function () {
    var $fuelSelect = $('#fuelFilter');
    var $arimrSelect = $('#arimrFilter');
    var $cropSelect = $('#cropFilter');

    $fuelSelect.change(function () {
        checkAllRows();
    });
    $arimrSelect.change(function () {
        checkAllRows();
    });
    $cropSelect.change(function () {
        checkAllRows();
    });
    clearArea();
    countVisibleArea();
    scaleFieldName();
    scalePlantName();
    calcuteNewArea();
    colorField();

});

function checkAllRows($select) {
    $('tbody').find("tr").each(function () {
        if ($(this).attr('name') == 'summary')
            return;
        $parcelFuel = $(this).find("td[name=fuel]").text();
        $parcelArimr = $(this).find("td[name=arimr]").text();
        $parcelCrop = $(this).find("td[name=crop]").text();
        $filterFuel = $('#fuelFilter').val();
        $filterArimr = $('#arimrFilter').val();
        $filterCrop = $('#cropFilter').val();
        if (isRowMatched($filterFuel, $parcelFuel) &&
                isRowMatched($filterArimr, $parcelArimr) &&
                isRowMatched($filterCrop, $parcelCrop)) {
            $(this).show();
        } else {
            $(this).hide();
        }

    });
    clearArea();
    countVisibleArea();
    scaleFieldName();
    scalePlantName();
    newNumerate();
    calcuteNewArea();
    colorField();
}
function isRowMatched($filterOption, $rowValue) {
    if ($filterOption == 'all')
        return true;

    if ($filterOption == $rowValue) {
        return true;
    } else {
        return false;
    }

}
function countVisibleArea() {
    $area = 0;
    $('tbody').find("tr").each(function () {
        if ($(this).is(":visible") && ($(this).attr('id') != 'summary')) {
            $area += +$(this).find("td[name=area]").text() * 100;
        }

    })
    $('#areaSum').html($area / 100 + ' ha');
}
function newNumerate() {
    $i = 1; // new counter
    $('tbody').find("tr").each(function () {
        if (isRowVisible($(this))) {
            $(this).find("td[name=no]").html($i);
            $i = $i + 1;
        }
    })

}
function scaleFieldName() {
    $firstFieldRow = null;
    $firstFieldName = null;
    $rowFieldName = null;
    $parcelCounter = 1;
    $size = getDisplayedRowAmount();
    $counter = 1;

    // Find all rows
    $('tbody').find("tr").each(function () {

        // Only displayed row
        if (isRowVisible($(this))) {
            // Initialize first fieldName
            // new number #

            if ($firstFieldRow == null) {
                $(this).find("td[name=fieldName]").show();
                $firstFieldRow = $(this);
                $counter++;
                return true;
            }

            $rowFieldName = $(this).find("td[name=fieldName]").text();
            $firstFieldName = $firstFieldRow.find("td[name=fieldName]").text();
            if ($firstFieldName == $(this).find("td[name=fieldName]").text()) {
                // that same field
                if ($firstFieldRow != $(this)) {
                    $parcelCounter++;
                    $(this).find("td[name=fieldName]").hide();


                    if ($counter == $size) {
                        // last iterate
                        spanRow($firstFieldRow, $parcelCounter, "fieldName");
                    }
                }
            } else {
                $(this).find("td[name=fieldName]").show();
                spanRow($firstFieldRow, $parcelCounter, "fieldName");
                // new field
                $parcelCounter = 1;
                $firstFieldRow = $(this);
            }



            $counter++;
        }
    })

}
function scalePlantName() {
    $firstFieldRow = null;
    $firstFieldName = null;
    $rowFieldName = null;
    $parcelCounter = 1;
    $size = getDisplayedRowAmount();
    $counter = 1;

    // Find all rows
    $('tbody').find("tr").each(function () {

        // Only displayed row
        if (isRowVisible($(this))) {
            // Initialize first fieldName
            if ($firstFieldRow == null) {
                $(this).find("td[name=crop]").show();
                $firstFieldRow = $(this);
                $counter++;
                return true;
            }

            $rowFieldName = $(this).find("td[name=crop]").text();
            $firstFieldName = $firstFieldRow.find("td[name=crop]").text();
            if ($firstFieldName == $(this).find("td[name=crop]").text()) {
                // that same field
                if ($firstFieldRow != $(this)) {
                    $parcelCounter++;
                    $(this).find("td[name=crop]").hide();


                    if ($counter == $size) {
                        // last iterate
                        spanRow($firstFieldRow, $parcelCounter, "crop");
                    }
                }
            } else {
                $(this).find("td[name=crop]").show();
                spanRow($firstFieldRow, $parcelCounter, "crop");
                // new field
                $parcelCounter = 1;
                $firstFieldRow = $(this);
            }

            $counter++;
        }
    })

}

function isRowVisible($row) {
    if ($row.is(":visible") && ($row.attr('name') != 'summary')) {
        return true;
    } else {
        return false;
    }

}
function spanRow($firstParcelRow, $count, $attrName) {
    $firstParcelRow.find("td[name=" + $attrName + "]").attr('rowspan', $count);
}
function getDisplayedRowAmount() {
    $amount = 0;

    $('tbody').find("tr").each(function () {
        // Only displayed row
        if (isRowVisible($(this))) {

            $amount++;
        }
    })

    return $amount;
}

function countVisibleFieldArea($fieldName) {
    $area = 0;
    $('tbody').find("tr").each(function () {
        if (isRowVisible($(this))) {
            $field = $(this).find("td[name=fieldName]").text();
            if ($field == $fieldName) {
                $area += $(this).find("td[name=area]").text() * 100;
            }

        }
    })
    return $area / 100;
}
function calcuteNewArea() {

    $('tbody').find("td[name=fieldName]").each(function () {
        if (isRowVisible($(this))) {
            $fieldName = $(this).html();
            $newArea = countVisibleFieldArea($fieldName);
            $(this).html($fieldName + '<span><br> [' + $newArea + ' ha] </span>');
        }
    })
}
function clearArea() {
    $('tbody').find("td[name=fieldName]").each(function () {
        $fieldName = $(this).html();
        $fieldName = getRawFieldName($fieldName);
        $(this).html($fieldName);
    })

}
function getRawFieldName($fieldName) {
    $toRemoveIndex = $fieldName.indexOf("<span><br>");
    if ($toRemoveIndex >= 0) {
        $fieldName = $fieldName.substr(0, $toRemoveIndex);
    }
    return $fieldName;
}

function colorField() {
    $even = false;
    $('tbody').find("td[name=fieldName]").each(function () {
        if (isRowVisible($(this))) {
            $fieldName = getRawFieldName($(this).html());
            if ($even) {
                $('tbody').find("td[name=fieldName").each(function () {
                    $thisName = getRawFieldName($(this).html());
                    if ($thisName == $fieldName) {

                        $(this).closest('tr').css('background-color', '#E8E8E8');
                    }
                });
                $even = false;
            } else {
                $('tbody').find("td[name=fieldName").each(function () {

                    $thisName = getRawFieldName($(this).html());
                    if ($thisName == $fieldName) {
                        $(this).closest('tr').css('background-color', '#FFF');
                    }
                });
                $even = true;
            }
        }

    })
}




