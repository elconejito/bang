/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import fontawesome from '@fortawesome/fontawesome-free'
import regular from '@fortawesome/fontawesome-free-regular'
import solid from '@fortawesome/fontawesome-free-solid'
import brands from '@fortawesome/fontawesome-free-brands'

fontawesome.library.add(regular)
fontawesome.library.add(solid)
fontawesome.library.add(brands)

$( document ).ready(function() {
  console.log('App Ready!');

  /**
   * Setup the Photo Upload DropZones
   */
  $('.picture-dropzone').each((i, dropzone) => {
    let dz = new Dropzone(dropzone, {
      parallelUploads: 1,
      maxFiles: 10,
      maxFilesize: 10,
      uploadMultiple: false,
      createImageThumbnails: false,
      previewTemplate: `<div class="dz-preview dz-file-preview">
    <div class="dz-details">
        <div class="dz-filename"><span data-dz-name></span></div>
        <div class="dz-size" data-dz-size></div>
    </div>
    <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress><div class="dz-status-message"></div></div>
    </div>
</div>`,
    });

    dz.on("drop", (file) => {
      console.log("Dropped file.");
    })
    .on("addedfile", (file) => {
      console.log("Added file.", file);
    })
    .on("removedfile", (file) => {
      console.log("Removed file.");
    })
    .on("error", (file, errorMessage, xhr) => {
      console.log("Error: " + errorMessage);
      $(dropzone).find('.progress-bar').addClass('bg-danger');
      $(dropzone).find('.dz-status-message').text("Error");
    })
    .on("processing", (file) => {
      console.log("Processing file.");
    })
    .on("uploadprogress", (file) => {
      console.log("uploadprogress.", file);
      $(dropzone).find('.dz-status-message').text(file.upload.progress + '%');
    })
    .on("sending", (file, xhr, formData) => {
      console.log("sending.", formData);
      $(dropzone).find('.dz-status-message').text('Sending.');
    })
    .on("success", (file, response) => {
      console.log("Success.", response);
      $(dropzone).find('.progress-bar').addClass('bg-success');
      $(dropzone).find('.dz-status-message').text('Uploaded.');
    })
    .on("complete", (file) => {
      console.log("Complete.", file);
    })
    .on("queuecomplete", () => {
      console.log("queuecomplete.", dz);
    });

  });
});
