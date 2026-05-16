import { registerBlockType } from "@wordpress/blocks";
import { __ } from "@wordpress/i18n";
import metadata from "../../block.json";
import Edit from "./edit";
import save from "./save";

registerBlockType(metadata.name, {
  ...metadata,
  edit: Edit,
  save,
});
