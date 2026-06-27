import {
  Spinner,
  Modal,
  Button,
  ComboboxControl,
  TextControl
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import '../../../index.css';

const SeriesSelector = ({
  selectedSeriesIds=[],
  activeSeriesId,
  onSetActiveSeries,
  seriesTerms,
  isLoading,
  onChangeSeries,
  onCreateSeries,
  hasSeriesBlock,
  onInsertSeriesBlock,
}) => {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [newSeriesName, setNewSeriesName] = useState('');

  const handleCreate = async () => {
    await onCreateSeries(newSeriesName);
    setNewSeriesName('');
    setIsModalOpen(false);
  };

  return (
    <div className="-mx-4">
      {isLoading && <Spinner />}

      {!isLoading && (
        <div>
          {/* Label */}
          <label className="block text-xs font-bold uppercase text-on-surface-variant mb-2">
            Series
          </label>

          {/* Combobox */}
          <div className="sm-series-combo mb-2">
            <ComboboxControl
            //value = "" to be add not replace
              value= ""
              options={[
                { value: '', label: 'Select the series' },
                ...seriesTerms.map((t) => ({
                  value: String(t.id),
                  label: t.name,
                })),
              ]}
              onChange={(value) =>{
                if (!value) return; // Prevent empty selection
                onChangeSeries(value)
              }}
            />
            <div className="flex flex-wrap gap-2 mt-2">
              {selectedSeriesIds.map((id) => {
                const series = seriesTerms.find(t => t.id === id);
                if (!series) return null;

                return (
                  <span
                    key={id}
                    onClick={() => onSetActiveSeries(id)}
                    className={`sm-setting-panal-icon px-2 py-1 text-xs rounded cursor-pointer flex items-center gap-1 ${
                      id === activeSeriesId
                        ? ' bg-primary/10 text-primary ring-1 ring-primary/20 px-3 py-1.5 rounded text-sm font-bold flex items-center gap-2 cursor-default'
                        : ' text-on-surface bg-surface-container-highest px-3 py-1.5 rounded text-sm font-medium flex items-center gap-2 group transition-all hover:bg-surface-container-low cursor-default'
                    }`}
                  >
                    {series.name}
                    <span
                      className="material-symbols-outlined text-sm opacity-60 group-hover:opacity-100 cursor-pointer"
                      onClick={(e) => {
                        e.stopPropagation();
                        onChangeSeries(id); // remove
                      }}
                    >
                      close
                    </span>
                  </span>
                );
              })}
            </div>
          </div>

          {/* New Series Button */}
          <div className="sm-new-series-action-container mb-4">
            <button
              type="button"
              className="sm-new-series-action px-3 py-1 text-sm font-medium text-primary rounded hover:bg-primary/5 transition-colors"
              onClick={() => {
                setNewSeriesName('');
                setIsModalOpen(true);
              }}
            >
              + New Series
            </button>
          </div>

          {selectedSeriesIds.length > 0 && (
            <div className="mb-4">
              <Button
                variant="secondary"
                onClick={onInsertSeriesBlock}
                disabled={hasSeriesBlock}
                className="w-full justify-center"
              >
                {hasSeriesBlock ? 'Series List block already added' : 'Add Series List block'}
              </Button>
            </div>
          )}

          {/* Modal */}
          {isModalOpen && (
            <Modal
              title="Add new series"
              onRequestClose={() => setIsModalOpen(false)}
              className="bg-surface-container-lowest border-outline-variant rounded-lg shadow-2xl overflow-hidden"
            >
              <TextControl
                label="Series name"
                value={newSeriesName}
                onChange={setNewSeriesName}
                className="mb-4"
              />

              <div className="flex justify-end gap-2">
                <Button
                  variant="secondary"
                  onClick={() => setIsModalOpen(false)}
                  className="px-4 py-2 text-sm font-medium text-on-surface bg-surface-container-highest rounded hover:bg-surface-container-low transition-colors"
                >
                  Cancel
                </Button>

                <Button
                  variant="primary"
                  onClick={handleCreate}
                  disabled={!newSeriesName}
                  className="px-4 py-2 text-sm font-medium text-surface-container-lowest bg-gradient-to-br from-primary to-primary-dim rounded shadow-sm transition-colors"
                >
                  ADD
                </Button>
              </div>
            </Modal>
          )}
        </div>
      )}
    </div>
  );
};

export { SeriesSelector };
