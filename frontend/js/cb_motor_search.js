jQuery(document).ready(function ($) {
  $(document).on("click", ".js-filter-item", function (e) {
    if (typeof cbMotorSearchAction !== "undefined") {
      e.preventDefault();
      let requestData = {
        action: "removeFilters",
      };
      cbMotorSearchAction(requestData);
    }
  });
});
