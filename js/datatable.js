/**
 * Data tables and CSV export.
 * Simple tables are used for 1 variable graphs. Crosstabs are for 2.
 * Each web page uses a separate set of functions: highlight, explorer, trend.
 */
"use strict";

function makeFilterString(age, gender, race, ethnicity) {
    let ages = ['18 - 24','25 - 34','35 - 44','45 - 54','55 - 64','65 - 74','75+'];
    let genders = ['Male','Female'];
    let races = ['White','Black','Asian','Native Hawaiian / Pacific Islander', 'American Indian / Alaska Native', 'Other', 'More than 1 race'];
    let ethnicities = ['Hispanic','Non-Hispanic'];

    let clauses = [];
    if(age!=null)
        clauses.push("Age Range = " + ages[age]);
    if(gender!=null)
        clauses.push("Gender = " + genders[gender]);
    if(race!=null)
        clauses.push("Race = " + races[race]);
    if(ethnicity!=null)
        clauses.push("Ethnicity = " + ethnicities[ethnicity]);

    if(clauses.length > 0)
        return "Filtered by " + clauses.join(", ");
    else
        return null;
}

function isIE() {
    return window.navigator.userAgent.indexOf("MSIE ") > 0 || !!window.navigator.userAgent.match(/Trident.*rv:11\./);
}

function getCSVHeader(mainTitle, groupTitle, year, filterString) {
    let csv = "Adult Gambling Behaviors Survey Data Explorer\r\n";
    csv += "Year: " + year + "\r\n";
    csv += '"' + mainTitle + '"\r\n';
    if(groupTitle != null)
        csv += '"Compared to Question: ' + groupTitle + '"\r\n';
    if(filterString != null)
        csv += '"' + filterString + '"\r\n';
    csv += "\r\n";

    return csv;
}

function simpleHighlightCSV(mainTitle, mainLabels, counts, totals, year) {
    let csv = getCSVHeader("Highlights: " + mainTitle, null, year, null);

    csv += ",Total Positive,Total Possible, % Positive\r\n";

    for(let i=0; i<mainLabels.length; i++)
    {
        csv += "\"" + mainLabels[i].replace("<br>"," ")+"\","+Math.round(counts[i][0]) + ",";
        csv += Math.round(totals[i]) + ",";
        csv += (counts[i][0]/totals[i]*100).toFixed(1) + "%\r\n";
    }

    tableToExcel(csv);
}

function simpleExplorerCSV(mainTitle, mainLabels, counts, totals, year, filterString) {
    let csv = getCSVHeader("Question: " + mainTitle, null, year, filterString);

    csv += ",Total,% Total\r\n";

    for(let i=0; i<mainLabels.length; i++)
    {
        csv += "\"" + mainLabels[i].replace("<br>"," ")+"\","+Math.round(counts[i][0]) + ",";
        csv += (counts[i][0]/totals[0]*100).toFixed(1) + "%\r\n";
    }
    csv += "Total," + Math.round(totals[0]) + ",100%";

    tableToExcel(csv);
}

function simpleTrendCSV(mainTitle, labels, xAxisLabels, percents, year, filterString, xAxisLabel) {
    let csv = getCSVHeader(mainTitle, null, year, filterString);

    csv += ","+xAxisLabel+"\r\n";
    for(let i=0; i<xAxisLabels.length; i++){
        csv += ','+xAxisLabels[i];
    }
    csv += "\r\n";

    for(let i=0; i<labels.length; i++)    {
        csv += '"'+labels[i]+'"';//escape commas
        for(let j=0; j<xAxisLabels.length; j++) {
            let val = (percents[j]['v'+i] != null) ? percents[j]['v'+i].toFixed(1)+'%' : 'N/A';
            csv += ',' + val;
        }
        csv += "\r\n";
    }

    tableToExcel(csv);
}

function crosstabHighlightCSV(mainTitle, groupTitle, mainLabels, groupLabels, counts, sumPositives, totals, year) {
    let csv = getCSVHeader("Highlights: " + mainTitle, groupTitle, year, null);

    csv += ',,"'+groupTitle+'"\r\n';
    csv += ",,"+groupLabels.join(",");
    csv += ",Total Positive,Total Possible, % Positive\r\n";
    csv += '"'+mainTitle+'"';

    for(let i=0; i<mainLabels.length; i++)
    {
        csv += ',"'+mainLabels[i].replace("<br>"," ")+'",';
        for(let j=0; j<groupLabels.length; j++) {
            csv += Math.round(counts[i][j]) + ",";
        }
        csv += Math.round(sumPositives[i]) + "," + Math.round(totals[i]) + "," + (sumPositives[i]/totals[i]*100).toFixed(1) + "%\r\n";
    }

    tableToExcel(csv);
}

function crosstabExplorerCSV(mainTitle, groupTitle, mainLabels, groupLabels, counts, totals, groupTotals, sumTotal, filterString, year) {
    let csv = getCSVHeader("Question: " + mainTitle, groupTitle, year, filterString);

    csv += ',,"'+groupTitle+'"\r\n';
    csv += ",,"+groupLabels.join(",");
    csv += ",Total,% Total\r\n";
    csv += '"'+mainTitle+'"';

    for(let i=0; i<mainLabels.length; i++)
    {
        csv += ',"'+mainLabels[i].replace("<br>"," ")+'",';
        for(let j=0; j<groupLabels.length; j++) {
            csv += Math.round(counts[i][j]) + ",";
        }
        csv += Math.round(totals[i]) + "," + (totals[i]/sumTotal*100).toFixed(1) + "%\r\n";
    }

    csv += ",Total,";
    for (let j = 0; j < groupLabels.length; j++) {
        csv += Math.round(groupTotals[j]) + ",";
    }
    csv += Math.round(sumTotal) + ",100%\r\n";
    csv += ",% Total,";
    for (let j = 0; j < groupLabels.length; j++) {
        csv += (groupTotals[j] / sumTotal * 100).toFixed(1) + "%,";
    }
    csv += "100%";

    tableToExcel(csv);
}

function tableToExcel(csv) {
    if(!isIE()) {
        let blob = new Blob([csv],{type: "text/csv;charset=utf-8;"});
        if (navigator.msSaveBlob) { // IE 10+
            navigator.msSaveBlob(blob, "inys-data.csv")
        } else {
            csv = "data:text/csv;charset=utf-8," + csv;
            let encodedUri = encodeURI(csv);
            let link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "inys-data.csv");
            document.body.appendChild(link); // Required for FF
            link.click();
        }
    }
    else {
        let blob = new Blob([csv],{type: "text/csv;charset=utf-8;"});
        if (navigator.msSaveBlob) { // IE 10+
            navigator.msSaveBlob(blob, "inys-data.csv")
        } else {
            let IEwindow = window.open();
            IEwindow.document.write('sep=,\r\n' + csv);
            IEwindow.document.close();
            IEwindow.document.execCommand('SaveAs', true, "inys-data.csv");
            IEwindow.close();
        }
    }
}

function createSimpleHighlightTable(tableElem, labels, counts, totals) {
    let table = $(tableElem);

    //add header in first row
    table.append('<tr><th class="clearcell">Category</th>' +
        '<th style="text-align: center">Total<br>Positive</th>' +
        '<th style="text-align: center">Total<br>Responses</th>' +
        '<th style="text-align: center">% Positive</th></tr>');

    //add a row for each answer
    for(let i=0; i<labels.length; i++) {
        let row = $('<tr></tr>').appendTo(table);
        row.append('<th>' + labels[i] + '</th>');
        row.append('<td>' + Math.round(counts[i][0]).toLocaleString() + '</td>');
        row.append('<td>' + Math.round(totals[i]).toLocaleString() + '</td>');
        row.append('<td>' + (counts[i][0]/totals[i]*100).toFixed(1) + '%</td>');
    }
}

function createSimpleExplorerTable(tableElem, labels, counts, sumTotal) {
    let table = $(tableElem);

    //add header in first row
    table.append('<tr><th class="clearcell">Answer</th>' +
        '<th style="text-align: center">Total</th>' +
        '<th style="text-align: center">% Total</th></tr>');

    //add a row for each answer
    for(let i=0; i<labels.length; i++) {
        let row = $('<tr></tr>').appendTo(table);
        row.append('<th>' + labels[i] + '</th>');
        row.append('<td>' + Math.round(counts[i]).toLocaleString() + '</td>');
        row.append('<td>' + (counts[i]/sumTotal*100).toFixed(1) + '%</td>');
    }

    //add total row
    table.append('<tr><th>Total</th>' +
        '<td>' + Math.round(sumTotal).toLocaleString() + '</td>' +
        '<td>100.0%</td></tr>');
}

function simpleTrendTable(tableElem, labels, xAxisLabels, percents, xAxisHeader) {
    let table = $(tableElem);

    //add "Year" in first row
    table.append('<tr><th class="clearcell" rowspan="2">Answer</th>' +
        '<th colspan="'+xAxisLabels.length+'" style="text-align: center">'+xAxisHeader+'</th></tr>');

    //add individual xAxisLabels as headers in second row
    let row = $('<tr></tr>').appendTo(table);
    for(let i=0; i<xAxisLabels.length; i++){
        row.append('<th>'+xAxisLabels[i]+'</th>');
    }

    //add each question as a row
    for(let i=0; i<labels.length; i++){
        let row = $('<tr></tr>').appendTo(table);
        row.append('<th>'+labels[i]+'</th>');
        for(let j=0; j<xAxisLabels.length; j++) {
            let val = (percents[j]['v'+i] != null) ? percents[j]['v'+i].toFixed(1)+'%' : 'N/A';
            row.append('<td>'+val+'</td>');
        }
    }
}

function createCrosstabHighlightTable(tableElem, mainTitle, groupTitle, mainLabels, groupLabels, counts, sumPositives, totals, skipSixthGrade) {
    let table = $(tableElem);

    //if skipping the 6th grade value, add one fewer column
    let startIndex = (skipSixthGrade) ? 1 : 0;
    let numGroups = (skipSixthGrade) ? groupLabels.length-1 : groupLabels.length;

    //add group title in first row
    let row = $('<tr></tr>').appendTo(table);
    row.append('<th colspan="2" rowspan="2" class="clearcell">Category</th>' +
        '<th colspan="'+numGroups+'" style="text-align: center">'+groupTitle+'</th>');

    row.append('<th rowspan="2" style="text-align: center">Total<br>Positive</th>' +
        '<th rowspan="2" style="text-align: center">Total<br>Possible</th>' +
        '<th rowspan="2" style="text-align: center">% Positive</th>');

    //add group answers in second row

    let groupHeader = $('<tr></tr>').appendTo(table);
    for(let i=startIndex; i<groupLabels.length; i++) {
        groupHeader.append('<th>'+groupLabels[i]+'</th>');
    }

    //add a row for each main let answers
    for(let i=0; i<mainLabels.length; i++) {
        let row = $('<tr></tr>').appendTo(table);

        //main title in first column
        if(i===0)
            row.append('<th style="width: 80px;" rowspan="'+mainLabels.length+'">'+mainTitle+'</th>');

        //answer label in second column, followed by data
        row.append('<th>'+mainLabels[i]+'</th>');
        for(let j=startIndex; j<groupLabels.length; j++) {
                row.append('<td>'+Math.round(counts[i][j]).toLocaleString()+'</td>');
        }

        //end row with total and percentage
        row.append('<td>'+Math.round(sumPositives[i]).toLocaleString()+'</td>' +
            '<td>' + Math.round(totals[i]).toLocaleString() + '</td>' +
            '<td>' + (sumPositives[i]/totals[i]*100).toFixed(1) + '%</td>');
    }
}

function createCrosstabExplorerTable(tableElem, mainTitle, groupTitle, mainLabels, groupLabels, counts, totals, groupTotals, sumTotal) {
    let table = $(tableElem);

    //add group title in first row
    let row = $('<tr></tr>').appendTo(table);
    row.append('<th colspan="2" rowspan="2" class="clearcell">Answer</th>' +
        '<th colspan="'+groupLabels.length+'" style="text-align: center">'+groupTitle+'</th>');

    row.append('<th rowspan="2" style="text-align: center">Total</th>' +
        '<th rowspan="2" style="text-align: center">% Total</th>');

    //add group answers in second row
    let groupHeader = $('<tr></tr>').appendTo(table);
    for(let i=0; i<groupLabels.length; i++) {
        groupHeader.append('<th>'+groupLabels[i]+'</th>');
    }

    //add a row for each main variable answer
    for(let i=0; i<mainLabels.length; i++) {
        row = $('<tr></tr>').appendTo(table);

        //main title in first column
        if(i === 0)
            row.append('<th style="width: 80px;" rowspan="'+mainLabels.length+'">'+mainTitle+'</th>');

        //answer label in second column, followed by data
        row.append('<th>'+mainLabels[i]+'</th>');
        for(let j=0; j<groupLabels.length; j++) {
            row.append('<td>'+Math.round(counts[i][j]).toLocaleString()+'</td>');
        }

        //end row with total and percentage
        row.append('<td>'+Math.round(totals[i]).toLocaleString() + '</td>' +
            '<td>' + (totals[i]/sumTotal*100).toFixed(1) + '%</td>');
    }

    //final two rows have the group totals
    row = $('<tr><th colspan="2">Total</th></tr>').appendTo(table);
    for (let i = 0; i < groupLabels.length; i++) {
        row.append('<td>' + Math.round(groupTotals[i]).toLocaleString() + '</td>');
    }
    row.append('<td>' + Math.round(sumTotal).toLocaleString() + '</td><td>100.0%</td>');

    row = $('<tr><th colspan="2">% Total</th></tr>').appendTo(table);
    for (let i = 0; i < groupLabels.length; i++) {
        row.append('<td>' + (groupTotals[i] / sumTotal * 100).toFixed(1) + '%</td>');
    }
    row.append('<td>100.0%</td>');
}