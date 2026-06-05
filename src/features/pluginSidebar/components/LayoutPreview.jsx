import "../../../index.css";
import { Icon } from "@wordpress/components";
import {
  media,
  link,
  formatListBullets,
  grid,
} from "@wordpress/icons";

export function LayoutPreview({ type }) {
  switch (type) {
    case "media-grid":
      return (
        <div className="sm-preview sm-preview--grid">
          {Array.from({ length: 6 }).map((_, i) => (
            <div key={i} className="sm-preview__cell">
              <Icon icon={media} />
            </div>
          ))}
        </div>
      );

    case "link-grid":
      return (
        <div className="sm-preview sm-preview--grid">
          {Array.from({ length: 6 }).map((_, i) => (
            <div key={i} className="sm-preview__cell">
              <Icon icon={link} />
            </div>
          ))}
        </div>
      );

    case "media-list":
      return (
        <div className="sm-preview sm-preview--list">
          {Array.from({ length: 3 }).map((_, i) => (
            <div key={i} className="sm-preview__row">
              <div className="sm-preview__media-icon">
                <Icon icon={media} />
              </div>

              <div className="sm-preview__line" />
            </div>
          ))}
        </div>
      );

    case "link-list":
      return (
        <div className="sm-preview sm-preview--list">
          {Array.from({ length: 3 }).map((_, i) => (
            <div key={i} className="sm-preview__row">
              <div className="sm-preview__media-icon">
                <Icon icon={link} />
              </div>

              <div className="sm-preview__line" />
            </div>
          ))}
        </div>
      );

    default:
      return null;
  }
}