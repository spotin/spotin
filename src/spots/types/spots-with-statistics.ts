import { Spot } from '@/spots/types/spot';
import { SpotsStatistics } from '@/spots/types/spots-statistics';

export type SpotsWithStatistics = {
	spotsStatistics: SpotsStatistics;
	spots: Spot[];
};
