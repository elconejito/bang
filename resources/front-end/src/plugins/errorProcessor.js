// import something here

// leave the export, even if you don't use it { app, router, store, Vue }
export default ({ Vue }) => {
  Vue.prototype.$errorProcessor = (error) => {
    console.error('ErrorProcessor() started', error);
    if (error.response) {
      // The request was made and the server responded with a status code
      // that falls out of the range of 2xx
      console.error('ErrorProcessor() data', error.response.data);
      console.error('ErrorProcessor() status', error.response.status);
      console.error('ErrorProcessor() headers', error.response.headers);

      // If this is a 401 unauthorized... bounce back to the login
      switch (error.response.status) {
        case 401:
          // #TODO Handle forced logout
          break;
        case 403:
          error.message = `You are not allowed to perform that action. ${error.message}`;
          break;
        default:
          break;
      }

      // If there is an error bag move it higher up into the object
      if (error.response.data && error.response.data.errors) {
        error.errorBag = error.response.data.errors;
      }
    } else if (error.request) {
      // The request was made but no response was received
      // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
      // http.ClientRequest in node.js
      console.error('ErrorProcessor() request', error.request);
    } else {
      // Something happened in setting up the request that triggered an Error
      console.error('ErrorProcessor() message', error.message);
    }

    return error;
  };
};
