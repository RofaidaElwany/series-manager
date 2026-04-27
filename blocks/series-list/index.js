import { registerBlockType } from "@wordpress/blocks";
import { __ } from "@wordpress/i18n";
import Edit from "./edit";
import save from "./save";

registerBlockType("series-manager/series-list", {
  title: __("Series List", "series-manager"),
  icon: "list-view",
  category: "widgets",
  attributes: {
    mode: {
      type: "string",
      default: "all",
    },
    limit: {
      type: "number",
      default: 5,
    },
    userId: {
      type: "string",
      default: "",
    },
  },
  edit: Edit,
  save: save,
});
