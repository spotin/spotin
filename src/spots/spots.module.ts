import { Module } from '@nestjs/common';
import { SpotsService } from './spots.service';
import { SpotsApiController } from './spots-api.controller';
import { PrismaModule } from 'src/prisma/prisma.module';
import { SpotsViewsController } from './spots-view.controller';

@Module({
  imports: [PrismaModule],
  providers: [SpotsService],
  controllers: [SpotsApiController, SpotsViewsController],
})
export class SpotsModule {}
