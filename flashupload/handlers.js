/* ����������: ���� ������ ���������� FileProgress �����, ������� ������������ ���������������� 
��������� ��� ����������� ����� ����� � ������� ����������. FileProgress ����� �� �������� ������ SWFUpload. */

/* ���������� ������� SWFUPLOAD */

function fileQueued(file) {
    try {
        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setStatus("��������...");
        progress.toggleCancel(true, this);

    } catch (ex) {
        this.debug(ex);
    }

}

function fileQueueError(file, errorCode, message) {
    try {
        if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
            alert("�� ��������� ��������� ������� ����� ������.\n" + (message === 0 ? "�� �������� ������ �������� ���� �� ����������." : "��� ����� ������� " + (message > 1 ? "�� " + message + " ������." : "������ ���� ����.")));
            return;
        }

        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setError();
        progress.toggleCancel(false);

        switch (errorCode) {
        case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
            progress.setStatus("������ ����� ��������� �����.");
            this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
            progress.setStatus("��������� ��������� ����� �������� ���� ����.");
            this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
            progress.setStatus("����� ��� ����� �� ��������������.");
            this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        default:
            if (file !== null) {
                progress.setStatus("����������� ������.");
            }
            this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        }
    } catch (ex) {
        this.debug(ex);
    }
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
    try {
        if (numFilesSelected > 0) {
            document.getElementById(this.customSettings.cancelButtonId).disabled = false;
        }
        this.startUpload(); /* ��������� �������� */
    } catch (ex)  {
        this.debug(ex);
    }
}

function uploadStart(file) {
    try {

  this.setPostParams({
  "msg": document.getElementById('txt_msg').value,
  "name": document.getElementById('txt_name').value,
  "email": document.getElementById('txt_email').value,
  "usernum": document.getElementById('txt_usernum').value});

        /* ���������� ���������������� ����������. ���������� ��� Linux-�������� */
        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setStatus("�����������...");
        progress.toggleCancel(true, this);
    }
    catch (ex) {}
    
    return true;
}

function uploadProgress(file, bytesLoaded, bytesTotal) {
    try {
        var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setProgress(percent);
// ��� �������. WR.
progress.setStatus("��������� <B>" + Math.round(bytesLoaded/bytesTotal*100) + "%</B> ����� " + file.name);

    } catch (ex) {
        this.debug(ex);
    }
}

function uploadSuccess(file, serverData) {
    try {
        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setComplete();
        progress.setStatus("�������� ���������.");
        progress.toggleCancel(false);

    } catch (ex) {
        this.debug(ex);
    }
}

function uploadError(file, errorCode, message) {
    try {
        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setError();
        progress.toggleCancel(false);

        switch (errorCode) {
        case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
            progress.setStatus("������ ��������: " + message);
            this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
            break;
        case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
            progress.setStatus("������ ��������.");
            this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        case SWFUpload.UPLOAD_ERROR.IO_ERROR:
            progress.setStatus("������ �����-������ ������� (IO Error).");
            this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
            break;
        case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
            progress.setStatus("������ ������� (� ����� ��������).");
            this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
            break;
        case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
            progress.setStatus("��������� ����� �� �������� ������.");
            this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
            progress.setStatus("������ ���������.  ����������� ���� ��������.");
            this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
            // If there aren't any files left (they were all cancelled) disable the cancel button
            if (this.getStats().files_queued === 0) {
                document.getElementById(this.customSettings.cancelButtonId).disabled = true;
            }
            progress.setStatus("�������� ��������");
            progress.setCancelled();
            break;
        case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
            progress.setStatus("�����������");
            break;
        default:
            progress.setStatus("����������� ������: " + errorCode);
            this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        }
    } catch (ex) {
        this.debug(ex);
    }
}

function uploadComplete(file) {
    if (this.getStats().files_queued === 0) {
        document.getElementById(this.customSettings.cancelButtonId).disabled = true;
    }
}

// ������� �� Queue Plugin
function queueComplete(numFilesUploaded) {
    var status = document.getElementById("divStatus");
    status.innerHTML = "<h1> ��������� " + numFilesUploaded + " ����. <A HREF='index.php'>��������� �� �������</a><br> ��� �������� ��������!</h1>";
}
