import { useEffect, useState } from '@wordpress/element';

export const useSeriesTerms = () => {
  const [seriesTerms, setSeriesTerms] = useState([]);
  const [isResolvingTerms, setIsResolvingTerms] = useState(true);

  useEffect(() => {
    const fetchTerms = async () => {
      try {
        setIsResolvingTerms(true);
        const formData = new URLSearchParams({
          action: 'sm_get_series_terms',
          nonce: SMSeries?.nonce || '',
        });

        const response = await fetch(SMSeries?.ajaxurl || '/wp-admin/admin-ajax.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: formData.toString(),
        });

        const data = await response.json();
        if (data.success) {
          setSeriesTerms(data.data);
        } else {
          setSeriesTerms([]);
          console.error('Failed to fetch series terms:', data.data?.message);
        }
      } catch (error) {
        console.error('Error fetching series terms:', error);
        setSeriesTerms([]);
      } finally {
        setIsResolvingTerms(false);
      }
    };

    fetchTerms();
  }, []);

  return {
    seriesTerms,
    isResolvingTerms,
  };
};
