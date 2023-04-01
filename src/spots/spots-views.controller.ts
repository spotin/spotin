import {
  Get,
  Controller,
  Render,
  UseGuards,
  Param,
  UseFilters,
  Res,
} from '@nestjs/common';
import { ApiTags } from '@nestjs/swagger';
import { JwtAuthGuard } from 'src/auth/jwt/jwt-auth-guards';
import { UnauthorizedExceptionFilter } from 'src/filters/unauthorized-exception.filter';
import { SpotsService } from './spots.service';
import * as qrcode from 'qrcode';
import { Response } from 'express';
import { ConfigService } from '@nestjs/config';

@ApiTags('Views')
@Controller('spots')
export class SpotsViewsController {
  constructor(
    private readonly spotsService: SpotsService,
    readonly configService: ConfigService,
  ) {}

  @UseGuards(JwtAuthGuard)
  @Get('list')
  @UseFilters(new UnauthorizedExceptionFilter())
  @Render('spots/list')
  async root() {
    const spots = await this.spotsService.getSpots();
    return { spots };
  }

  @Get('latest')
  @Render('spots/latest')
  async getLatestSpots() {
    const spots = await this.spotsService.getPublicSpots();
    return { spots };
  }

  @Get('govtech')
  @Render('one-page/index')
  async getOnePage() {
    const spots = await this.spotsService.getSpots();
    return { spots };
  }

  @Get(':uuid/delete')
  @Render('spots/list')
  async deleteSpot(@Param('uuid') uuid: string) {
    await this.spotsService.deleteSpot(uuid);
    const spots = await this.spotsService.getSpots();
    return { spots };
  }

  @Get(':uuid/edit')
  @Render('spots/form')
  async editSpot(@Param('uuid') uuid: string) {
    const spot = await this.spotsService.getSpot(uuid);
    return { values: spot, action: 'PATCH', uuid };
  }

  @Get('create')
  @Render('spots/form')
  createSpot() {
    return { action: 'POST' };
  }

  @UseGuards(JwtAuthGuard)
  @Get(':uuid')
  @UseFilters(new UnauthorizedExceptionFilter())
  async getSpot(@Res() res: Response, @Param('uuid') uuid: string) {
    const spot = await this.spotsService.getSpot(uuid);

    if (spot) {
      const backendUrl = this.configService.get('SPOT_IN_BACKEND_URL');
      const redirection = `${backendUrl}/api/spots/${spot.id}/redirect`;

      qrcode
        .toDataURL(redirection)
        .then((url) => {
          res.render('spots/[uuid]', {
            title: 'Spot',
            spot: spot,
            qrcode: url,
          });
        })
        .catch((err) => {
          console.error(err);
        });
    }
  }
}
