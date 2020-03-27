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
    removeOldTableIfExist();
    addFilteredTBody();
    scaleFieldNames();
    scalePlantNames();
    colorFields();
    addSummaryRow();
    addAreaToFields();
    renumerate();
}
function removeOldTableIfExist() {
    $oldTable = $('#parcels').find('tbody[filteredTable]')
     if( $oldTable.length ) {
        $oldTable.remove();
    }
}
function isValueMatched($required, $value) {
    if($required == 'all') return true;
    if($required == $value) 
        return true;
    return false
}
function getFilteredTBody($isFuel, $arimrOperator, $plantName) {
    removeOldTableIfExist();
    
    $data = $('#parcels').find('tbody[data]');
    $outputTable = '<tbody filteredTable>';

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
        $('#parcels > tbody').after($newTBody);
}
function amountOfParcelsByFieldName($fieldName) {
    $counter = 0;
	$('tbody[filteredTable]').find('tr').each(function() {
		if( $(this).find('td[name=fieldName]').text() == $fieldName ) {
			$counter = $counter+1;
	}
		})
	
    return $counter;
	
}
function amountOfParcelsByPlantName($plantName) {
    $counter = 0;
	$('tbody[filteredTable]').find('tr').each(function() {
		if( $(this).find('td[name=plantName]').text() == $plantName ) {
			$counter = $counter+1;
	}
		})
	
    return $counter;
}
function scaleFieldNames() {
    $fieldsTD = $('tbody[filteredTable]').find('td[name=fieldName]');
    $previousFieldName = null;
    $fieldsTD.each(function() {
        $thisFieldNameTD = $(this);
        if($previousFieldName == $thisFieldNameTD.text()) {
            $(this).hide();
        } else {
            $thisFieldNameTD.attr('rowspan', amountOfParcelsByFieldName($thisFieldNameTD.text()));
            $previousFieldName = $thisFieldNameTD.text();
        }
        
        
    })
}
function scalePlantNames() {
    $plantsTD = $('tbody[filteredTable]').find('td[name=plantName]');
    $previousPlantName = null;
    $plantsTD.each(function() {
        $thisPlantNameTD = $(this);
        if($previousPlantName == $thisPlantNameTD.text()) {
            $(this).remove();
        } else {
            $thisPlantNameTD.attr('rowspan', amountOfParcelsByPlantName($thisPlantNameTD.text()));
            $previousPlantName = $thisPlantNameTD.text();
        }
        
        
    })
}
function getArrayWithFieldNames() {
    $fieldsTD = $('tbody[filteredTable]').find('td[name=fieldName]'); 
    $fields = [];
    $fieldsTD.each(function() {
                   $fieldName = $(this).text();
        if($.inArray($fieldName,$fields) == -1) {
            $fields.push($fieldName);
        }}
    )
    return $fields;
}
function colorFields() {
    $TDfields = $('tbody[filteredTable]');
    $even = false;
    $fields = getArrayWithFieldNames();
    
    for(i=0;i<$fields.length;i++) {
        if($even) {        
            // Even row
            $TDfields.find('td[name=fieldName]:contains('+$fields[i]+')').each(function() {
                $(this).closest('tr').css('background-color', '#E8E8E8');
            })
            $even = false;
        } else {
            // Odd row
            $TDfields.find('td[name=fieldName]:contains('+$fields[i]+')').each(function() {
                //$(this).closest('tr').css('background-color', '#FFF');
            })
            $even = true;
        }
           
        }
    }
function getFieldArea($fieldName) {
    $area = 0;
    //console.log($('tbody[filteredTable]').find('td[name=fieldName]:contains('+$fieldName+')').siblings()); 
    $('tbody[filteredTable]').find('tr').each(function() {
        if( $(this).find('td[name=fieldName]').text() == $fieldName) {
            $area += $(this).find('td[name=area]').text()*100;
        }
        
    })
    return ($area/100).toFixed(2);
}
function getTotalArea() {
    $area = 0;
    $('tbody[filteredTable]').find('td[name=area]').each(function() {
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
    $('tbody[filteredTable]').append($html);
}
function addAreaToFields() {
    $('tbody[filteredTable]').find('td[name=fieldName]').each(function() {
        $(this).append('<br />['+getFieldArea($(this).text())+' ha]');
    })
}
function renumerate() {
    $counter = 1;
    $('tbody[filteredTable]').find('td[name=no]').each(function() {
        $(this).html($counter);
        $counter = $counter + 1;
    })
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

