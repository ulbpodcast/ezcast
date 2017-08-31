var slices = {'cam': 0, 'slide': 0}; // slices, value that gets decremented
var slicesTotal = {'cam': 0, 'slide': 0}; // total amount of slices, constant once calculated
var finished = {'cam': 0, 'slide': 0}; // global variable that contains the number of chunks that have been uploaded
var globalObj = new Object();

/**
 * Calculates slices and indirectly uploads a chunk of a file via uploadFile()
 **/
function process(file) {
    var blob = file.blob;
    var type = file.type;

    var start = 0;
    var end;
    var index = 0;

    // calculate the number of slices 
    slices[type] = Math.ceil(blob.size / globalObj.chunkSize);
    slicesTotal[type] = slices[type];
    
    msg('console', 'start');
    msg("exec", 'updateProgress(' + 0 + ', "' + type + '");');

    while (start < blob.size) {
        end = start + globalObj.chunkSize;
        if (end > blob.size) {
            end = blob.size;
        }

        uploadFile(file, index, start, end);

        start = end;
        index++;
    }
}

/**
 * Blob to ArrayBuffer (needed ex. on Android 4.0.4)
 **/
var str2ab_blobreader = function(str, callback) {
    var blob;
    if (typeof(window) == 'undefined') {
        BlobBuilder = window.MozBlobBuilder || window.WebKitBlobBuilder || window.BlobBuilder;
    }
    if (typeof(BlobBuilder) !== 'undefined') {
        var bb = new BlobBuilder();
        bb.append(str);
        blob = bb.getBlob();
    } else {
        blob = new Blob([str]);
    }
    var f = new FileReader();
    f.onload = function(e) {
        callback(e.target.result);
    };
    f.readAsArrayBuffer(blob);
};

/**
 * Performs actual upload, adjustes progress bars
 *
 * @param file
 * @param index
 * @param start
 * @param end
 */
function uploadFile(file, index, start, end) {
    var xhr;
    var chunk;
    var blob = file.blob;
    var type = file.type;
    
    xhr = new XMLHttpRequest();

    if (blob.slice) {
        chunk = blob.slice(start, end);
    } else if (blob.mozSlice) {
        chunk = blob.mozSlice(start, end);
    } else {
        chunk = blob.webkitSlice(start, end);
    }

    // passes here each time a chunk of file has been fully uploaded
    xhr.addEventListener("load", function(evt) {
        response = eval("(" + xhr.responseText + ")");
        // if response.error is set, an error occured server side
        if (response.error) {
            msg('error', response.error);
        }
        finished[type]++;
        progressRate = Math.round(finished[type] / slicesTotal[type] * 100);
        //    console.log(['xhr upload complete', evt] + " - " + progressRate + "%");
        msg('console', type + ' [' + finished[type] + '/' + slicesTotal[type] + '] xhr upload complete : ' + progressRate + '%');

        // updates the progress bar
        if (progressRate != 100)
            msg("exec", "updateProgress(" + progressRate + ", '" + type +"');");

        slices[type]--;

        // if we have finished all slices
        if (slices[type] == 0) {
            mergeFile(file);
        }

    }, false);

    xhr.addEventListener("error", function(e) {
        msg("error", "Error while uploading chunk " + index + " of " + slicesTotal[type]);
    }, false);

    // passes here each time a chunch of file is being uploaded
    /*     xhr.upload.addEventListener("progress", function(evt) {
     if (evt.lengthComputable) {
     progressRate = Math.round(index / slicesTotal * 100);
     console.log("total upload - " + progressRate + "%");
     }
     }, false);  */


    xhr.open("post", globalObj.url + "/index.php?action=upload_chunk", false);
    xhr.setRequestHeader("X-Index", index);                     // part identifier
    xhr.setRequestHeader("X-id", globalObj.id);
    xhr.setRequestHeader("X-type", type);

    // android default browser in version 4.0.4 has webkitSlice instead of slice()
    if (blob.webkitSlice && typeof(blob.slice) !== 'function') {                                     
        var buffer = str2ab_blobreader(chunk, function(buf) {   // we cannot send a blob, because body payload will be empty
            xhr.send(buf);                                      // thats why we send an ArrayBuffer
        });
    } else {
        xhr.send(chunk);                                        // but if we support slice() everything should be ok
    }
}

/**
 *  Function executed once all of the slices has been sent, "TO MERGE THEM ALL!"
 **/
function mergeFile(file) {
    var xhr;
    var blob = file.blob;
    var type = file.type;
    var params = "id=" + globalObj.id +
            "&index=" + slicesTotal[type]+
            "&type=" + type;

    xhr = new XMLHttpRequest();

    xhr.addEventListener("load", function(evt) {
        response = eval("(" + xhr.responseText + ")");
        // if response.error is set, an error occured server side
        if (response.error) {
            msg('error', response.error);
        } else if (response.wait){
            msg('console', '[' + type + ' uploaded] wait until all files are uploaded');
            return false;
        }

        msg("exec", 'updateProgress(' + 100 + ', "");');
        msg('console', 'full upload finished');

    }, false);
    xhr.open("POST", globalObj.url + "/index.php?action=upload_finished", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.send(params);
}

self.onmessage = function(e) {
    switch (e.data.fct) {
        case 'pushValue':
            self.postMessage(e.data.fct);
            globalObj[e.data.args.key] = e.data.args.value;
            msg("console", "globalObj[" + e.data.args.key + "] : " + e.data.args.value);
            break;
            
        case 'process':
            msg("console", e.data.fct);
            var file = e.data.args;
            process(file);
            break;
    }
};

function msg(action, message) {
    self.postMessage({'action': action, 'message': message}); // exec / console / error
}