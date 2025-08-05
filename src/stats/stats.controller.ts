import { Controller } from '@nestjs/common';
import { StatsService } from '@/stats/stats.service';
import { ApiTags } from '@nestjs/swagger';
import { GetMany } from '@/common/decorators/get-many.decorator';
import { ReadStatsDto } from '@/stats/dtos/read-stats.dto';

@ApiTags('Stats')
@Controller('api/stats')
export class StatsController {
	constructor(private readonly statsService: StatsService) {}

	@GetMany({
		name: 'Stats',
		summary: 'Get the stats',
		operationId: 'getStats',
		responseType: ReadStatsDto,
	})
	async getStats(): Promise<ReadStatsDto> {
		const stats = await this.statsService.getStats();

		return new ReadStatsDto(stats);
	}
}
