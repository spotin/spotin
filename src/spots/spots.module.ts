import { Module } from '@nestjs/common';
import { SpotsService } from './spots.service';
import { SpotsApiController } from './spots-api.controller';
import { PrismaModule } from 'src/prisma/prisma.module';
import { SpotsViewsController } from './spots-views.controller';
import { ConfigModule } from '@nestjs/config';

@Module({
  imports: [PrismaModule, ConfigModule],
  providers: [SpotsService],
  controllers: [SpotsApiController, SpotsViewsController],
})
export class SpotsModule {}
