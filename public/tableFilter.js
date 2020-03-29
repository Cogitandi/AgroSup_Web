$(document).ready(function() {
    var $fuelSelect = $('#fuelSelect');
    var $arimrSelect = $('#arimrOperatorSelect');
    var $cropSelect = $('#plantNameSelect');

    $fuelSelect.change(function () {
        tableActions();
    });
    $arimrSelect.change(function () {
        tableActions();
    });
    $cropSelect.change(function () {
        tableActions();
    });
    tableActions();

})

function tableActions() {
    removeOldTableIfExist() 
    addFilteredTBody();

    $filteredTBody = $('#filteredTable');
    $filteredTR = $('#filteredTable').find('tr');
    $filteredFieldsNameTD = $filteredTR.find('td[name=fieldName]');
    $filteredPlantNameTD = $filteredTR.find('td[name=plantName]');
    $fieldsWithArea = fieldsWithArea();

    scaleFieldNames();
    scalePlantNames();

    colorFields();
    colorPlants();
    renumerate();
    addSummaryRow();
    addAreaToFields();
}


function removeOldTableIfExist() {
    $filteredTBody = $('#filteredTable');
    if( $filteredTBody.length) {
        $filteredTBody.remove() // remove old table
    }
}

function isValueMatched($required, $value) {
    if($required == 'all') return true;
    if($required == $value) 
        return true;
    return false
}
function getFilteredTBody($isFuel, $arimrOperator, $plantName) {
    $data = $('#rawTable');
    $outputTable = '<tbody id=filteredTable>';

    $data.find('tr').each( function() {
        $rowFuel = $(this).find('td[name=fuel]').text();
        $rowArimrOperator = $(this).find('td[name=arimrOperator]').text();
        $rowPlantName = $(this).find('td[name=plantName]').text();
        if(isValueMatched($isFuel,$rowFuel) &&
           isValueMatched($arimrOperator,$rowArimrOperator) &&
           isValueMatched($plantName,$rowPlantName)) {
            $outputTable += '<tr>'+$(this).html()+'</tr>';
        }
    })
    $outputTable += '</tbody>';
    return $outputTable;

}
function addFilteredTBody() {
    $choosedFuel = $('#fuelSelect').val();
    $choosedArimr = $('#arimrOperatorSelect').val();
    $choosedCrop = $('#plantNameSelect').val();
    $newTBody = getFilteredTBody($choosedFuel,$choosedArimr,$choosedCrop);
    $('#rawTable').after($newTBody);
}
function scaleFieldNames() {
    $firstFieldNameTD = $filteredFieldsNameTD.first();
    $counter = 0;
    $filteredFieldsNameTD.each(function() {
        if( $firstFieldNameTD.text() == $(this).text() ) {
            $counter = $counter + 1;

            if($counter != 1) {
                $(this).hide();
            }

            if($(this).is($filteredFieldsNameTD.last()) ) {
                $firstFieldNameTD.attr('rowspan', $counter);
                return;
            }


        } else {
            $firstFieldNameTD.attr('rowspan', $counter);
            $firstFieldNameTD = $(this);
            $counter = 1;
        }

    })
}
function scalePlantNames() {
    $firstPlantNameTD = $filteredPlantNameTD.first();
    $counter = 0;
    $filteredPlantNameTD.each(function() {
        if( $firstPlantNameTD.text() == $(this).text() ) {
            $counter = $counter + 1;

            if($counter != 1) {
                $(this).remove();
            }

            if($(this).is($filteredPlantNameTD.last()) ) {
                $firstPlantNameTD.attr('rowspan', $counter);
                return;
            }


        } else {
            $firstPlantNameTD.attr('rowspan', $counter);
            $firstPlantNameTD = $(this);
            $counter = 1;
        }

    })
}
function getArrayWithFieldNames() {
    $fields = [];
    $filteredFieldsNameTD.each(function() {
        $fieldName = $(this).text();
        if($.inArray($fieldName,$fields) == -1) {
            $fields.push($fieldName);
        }}
                              )
    return $fields;
}
function getArrayWithPlantNames() {
    $plantNames = [];
    $filteredPlantNameTD.each(function() {
        $plantName = $(this).text();
        if($.inArray($plantName,$plantNames) == -1) {
            $plantNames.push($plantName);
        }}
                             )
    return $plantNames;
}
function colorFields() {
    $even = false;
    $fields = getArrayWithFieldNames();

    for(i=0;i<$fields.length;i++) {
        $found = false;
        if($even) {        
            // Even row
            $filteredTR.each(function() {
                if( $(this).find('td[name=fieldName]').text() == $fields[i] ) {
                    $(this).children().each(function() {
                        if($(this).attr('name=plantName')) return true;
                        
                        $(this).css('background-color', '#E8E8E8');
                    })
                    $found = true;
                } else {
                    if($found) return false;
                }
            })
            $even = false;
        } else {
            // Odd row
            $even = true;
        }

    }
}
function colorPlants() {
    $plantNames = getArrayWithPlantNames();

    for(i=0;i<$plantNames.length;i++) {
        $color = generateColor();
        $filteredPlantNameTD.each(function() {
            if( $(this).text() == $plantNames[i] ) {
                $(this).css('background-color', $color);
            } 
        })
    }

}
function getFieldArea($fieldName) {
    $area = 0;
    $found = false;
    $filteredTR.find(':contains('+$fieldName+')').each(function() {
        if( $(this).text() == $fieldName) {
            $found = true;
            $area += $(this).closest('tr').find('td[name=area]').text()*100;

        } else {
            if($found) return false;
        }
    })
    return ($area/100).toFixed(2);
}
function fieldsWithArea() {
    $fieldNames = getArrayWithFieldNames();
    $fieldsWithArea = {};
    for(i=0;i<$fieldNames.length;i++) {
        $fieldsWithArea[$fieldNames[i]] = 0;
    }
    $filteredTR.each(function() {
        $fieldName = $(this).find('td[name=fieldName]').text();
        $area = $(this).find('td[name=area]').text()*100;
        $fieldsWithArea[$fieldName] += $area;

    })
    return $fieldsWithArea;


}
function getTotalArea() {
    $area = 0;
    $filteredTR.find('td[name=area]').each(function() {
        $area += $(this).text()*100;
    })
    return ($area/100).toFixed(2);
}
function addSummaryRow() {
    $html = '<tr>';
    $html += '<td></td>';
    $html += '<td></td>';
    $html += '<td class="font-weight-bold">Razem</td>';
    $html += '<td class="font-weight-bold">'+getTotalArea()+'</td>';
    $html += '<td></td>';
    $html += '<td></td>';
    $html += '<td></td>';
    $html += '</tr>';
    $filteredTBody.append($html);
}
function addAreaToFields() {
    $filteredFieldsNameTD.each(function() {
        $thisFieldName = $(this).text();

        if( Object.keys($fieldsWithArea).length==0) return false;

        if( $fieldsWithArea[ $thisFieldName ] === undefined) return true;

        $(this).append('<br />['+($fieldsWithArea[$thisFieldName]/100).toFixed(2)+' ha]');
        delete $fieldsWithArea[ $thisFieldName ];
    })
}
function renumerate() {
    $counter = 1;
    $filteredTR.find('td[name=no]').each(function() {
        $(this).text($counter);
        $counter = $counter + 1;
    })
}
function generateColor() {
    var colorR = Math.floor((Math.random() * 256));
    var colorG = Math.floor((Math.random() * 256));
    var colorB = Math.floor((Math.random() * 256));
    return "rgb(" + colorR + "," + colorG + "," + colorB + ",0.5)";
}

//    
//    $fieldsTD.each(function() {
//        if($even) {
//            $fieldsTD.find(':contains('+$(this).text()+')').each(function() {
//                $(this).closest('tr').css('background-color', '#E8E8E8');
//            })
//            $even = false;
//        } else {
//            $fieldsTD.find(':contains('+$(this).text()+')').each(function() {
//                 $(this).closest('tr').css('background-color', '#FFF');
//            })
//           
//            $even = true;
//        }
//    })


/*
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


*/

