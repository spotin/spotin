import { StatsDto } from '@/stats/dtos/stats.dto';
import { ReadStats } from '@/stats/types/read-stats';
import { Stats } from '@/stats/types/stats';

export class ReadStatsDto extends StatsDto implements ReadStats {
	constructor(entity: Stats) {
		super();

		this.numberOfUsers = entity.numberOfUsers;
		this.numberOfCertifiedUsers = entity.numberOfCertifiedUsers;
		this.numberOfPublicSpots = entity.numberOfPublicSpots;
	}
}
