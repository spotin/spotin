import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { PrismaModule } from 'nestjs-prisma';
import { SpotsService } from '@/spots/spots.service';
import { SpotsApiController } from '@/spots/spots-api.controller';
import { SpotsMvcController } from '@/spots/spots-mvc.controller';

@Module({
	imports: [ConfigModule, PrismaModule],
	providers: [SpotsService],
	controllers: [SpotsApiController, SpotsMvcController],
	exports: [SpotsService],
})
export class SpotsModule {}
