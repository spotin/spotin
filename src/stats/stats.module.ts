import { Module } from '@nestjs/common';
import { StatsService } from '@/stats/stats.service';
import { StatsController } from '@/stats/stats.controller';
import { PrismaModule } from 'nestjs-prisma';

@Module({
	imports: [PrismaModule],
	controllers: [StatsController],
	providers: [StatsService],
	exports: [StatsService],
})
export class StatsModule {}
